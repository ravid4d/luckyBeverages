<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use QuickBooksOnline\API\DataService\DataService;
use Carbon\Carbon;
use App\Services\QuickBookService;

class QuickBookController extends Controller
{
    // public $quickBook;
    // public function __construct() {
    //     $this->quickBook = DataService::Configure([
    //         'auth_mode'    => 'oauth2',
    //         'ClientID'     => env('QUICKBOOKS_CLIENT_ID'),
    //         'ClientSecret' => env('QUICKBOOKS_CLIENT_SECRET'),
    //         'RedirectURI'  => env('QUICKBOOKS_REDIRECT_URI'),
    //         'scope'        => 'com.intuit.quickbooks.accounting',
    //         'baseUrl'      => env('QUICKBOOKS_ENV') === 'production' ? "Production" : "sandbox"
    //     ]);
    // }
    protected $quickBookService;

    public function __construct(QuickBookService $quickBookService)
    {
        $this->quickBookService = $quickBookService;
    }
    public function call()
    {
        $dataService = DataService::Configure([
            'auth_mode'    => 'oauth2',
            'ClientID'     => env('QUICKBOOKS_CLIENT_ID'),
            'ClientSecret' => env('QUICKBOOKS_CLIENT_SECRET'),
            'RedirectURI'  => env('QUICKBOOKS_REDIRECT_URI'),
            'scope'        => 'com.intuit.quickbooks.accounting',
            'baseUrl'      => env('QUICKBOOKS_ENV') === 'production' ? "Production" : "sandbox"
        ]);
        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();

        return redirect()->away($authUrl); 
    }
    public function callback(Request $request)
    {
        $OAuth2LoginHelper = $this->quickBook->getOAuth2LoginHelper();
        $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($request->code, $request->realmId);

        $accessToken1 = $accessToken->getAccessToken();
        $refreshToken = $accessToken->getRefreshToken();
        // $accessTokenExpiresAt = Carbon::now()->addSeconds($accessToken->getAccessTokenExpiresIn());
        // $refreshTokenExpiresAt = Carbon::now()->addDays(100); // QuickBooks refresh tokens usually last 100 days
        $accessTokenExpiresAt = $accessToken->getAccessTokenExpiresAt(); // Returns UNIX timestamp
        $refreshTokenExpiresAt = $accessToken->getRefreshTokenExpiresAt(); // Returns UNIX timestamp
        // dd($accessToken);
        return response()->json([
        "accessToken"=>$accessToken1,
        "refreshToken"=>$refreshToken,
        "accessTokenExpiresAt"=>$accessTokenExpiresAt ,                                                                                                                                                          
        "refreshTokenExpiresAt"=>$refreshTokenExpiresAt
    ]);                                                                                                                          
        
    }
    public function refreshToken()
    {
        
        $OAuth2LoginHelper = $this->quickBook->getOAuth2LoginHelper();
        $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken("AB11731586051u219peVmsKCccEzBJTidwJWpmHxNC95SNBpQt", "9341453270870456");
        return $accessToken;
    }
    public function customer()
    {
        $data = $this->quickBookService->getDataService()->Query('select * from Customer');
        return view('customer',["customer"=>$data]);
        // return $data;
    }
    public function invoice()
    {
        $data = $this->quickBookService->getDataService()->Query('select * from Invoice');
        return $data;
    }
}
