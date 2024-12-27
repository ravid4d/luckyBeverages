<?php
namespace App\Services;

use App\Models\QuickBookToken;
use Carbon\Carbon;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;

class QuickBookService
{
    protected $dataService;

    public function __construct()
    {
        $this->configureQuickBooks();
    }

    /**
     * Configures the QuickBooks DataService using the stored tokens
     */
    public function configureQuickBooks()
    {
        $token = QuickBookToken::first(); // Assume a single row in the database

        if (!$token) {
            throw new \Exception("No QuickBooks tokens found. Please connect QuickBooks.");
        }

        $this->validateTokens($token); // Ensure tokens are valid

        // Configure DataService
        $this->dataService = DataService::Configure([
            'auth_mode'       => 'oauth2',
            'ClientID'        => env('QUICKBOOKS_CLIENT_ID'),
            'ClientSecret'    => env('QUICKBOOKS_CLIENT_SECRET'),
            // 'RedirectURI'     => env('QUICKBOOKS_REDIRECT_URI'),
            // 'scope'           => 'com.intuit.quickbooks.accounting',
            'baseUrl'         => 'Development',
            'QBORealmID'      => env('QUICKBOOKS_REALMID'),
            'accessTokenKey'  => $token->accessToken,
            'refreshTokenKey' => $token->refreshToken,
        ]);
    }

    /**
     * Validates the access token and refresh token
     * @param QuickBookToken $token
     */
    protected function validateTokens($token)
    {
        
        // $currentTimestamp = Carbon::now()->timestamp;
        $accessTokenExpireAt = Carbon::parse($token->accessTokenExpireAt);
        $currentTimestamp = Carbon::now();

        // Check if access token has expired
        // dd($accessTokenExpireAt."".$currentTimestamp);
        if ($currentTimestamp->greaterThan($accessTokenExpireAt)) {
            // dd("hello access token expired");
            $this->refreshAccessToken($token);
        }

        

        // Check if refresh token has expired
        if ($currentTimestamp > $token->refreshTokenExpireAt) {
            // dd('hello');
            $this->refreshRefreshToken($token);
        }
    }

    /**
     * Refreshes the access token using the refresh token
     * @param QuickBookToken $token
     */
    protected function refreshAccessToken($token)
    {
        // $newToken = $this->dataService->refreshOAuth2Token();

        $oauth2LoginHelper = new OAuth2LoginHelper(env('QUICKBOOKS_CLIENT_ID'),env('QUICKBOOKS_CLIENT_SECRET'));
        $accessTokenObj = $oauth2LoginHelper->refreshAccessTokenWithRefreshToken($token->refreshToken);
        $accessTokenValue = $accessTokenObj->getAccessToken();
        $refreshTokenValue = $accessTokenObj->getRefreshToken();

        $token->update([
            'accessToken' => $accessTokenObj->getAccessToken(),
            'accessTokenExpireAt' => $accessTokenObj->getAccessTokenExpiresAt(),
        ]);
        // dd("done insertion");

        // Reconfigure DataService with the new token
        $this->configureQuickBooks();
    }

    /**
     * Refreshes the refresh token using the current refresh token
     * @param QuickBookToken $token
     */
    protected function refreshRefreshToken($token)
    {
        // Assuming DataService supports token refresh using the current refresh token
        $newToken = $this->dataService->refreshOAuth2Token();
        $oauth2LoginHelper = new OAuth2LoginHelper(env('QUICKBOOKS_CLIENT_ID'),env('QUICKBOOKS_CLIENT_SECRET'));
        $revokeResult = $oauth2LoginHelper->revokeToken($token->refreshToken);

        $token->update([
            'refreshToken' => $revokeResult->getRefreshToken(),
            'accessToken' => $revokeResult->getAccessToken(),
            'accessTokenExpireAt' => $revokeResult->getAccessTokenExpiresAt(),
            'refreshTokenExpireAt' => $revokeResult->getRefreshTokenExpiresAt(),
        ]);

        // Reconfigure DataService with the new token
        $this->configureQuickBooks();
    }

    /**
     * Get the configured QuickBooks DataService
     * @return DataService
     */
    public function getDataService()
    {
        return $this->dataService;
    }
}
