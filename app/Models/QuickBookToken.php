<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuickBookToken extends Model
{
    protected $table = 'quickbooktokens';
    public $timestamps = false;
    protected $fillable = ['refreshToken', 'accessToken', 'refreshTokenExpireAt', 'accessTokenExpireAt'];
}
