<?php

namespace App\Http\Controllers\v2;

use App\Models\User;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\RateLimiter;
use App\Traits\ThrottlesAttempts;

class UserAuthController extends Controller
{
    use ThrottlesAttempts;

    public function login(Request $request)
    {
        // Memeriksa apakah terlalu banyak percobaan login
        if ($this->hasTooManyAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        // validasi input
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);        
        
        if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
            $user = \Illuminate\Support\Facades\Auth::user();

            // clear attempts
            $this->clearAttempts($request);

            // $scopes = \App\Models\RsiaUserScopes::where('nik', $user->id_user)->get();
            // $scopes->pluck('scope')->toArray();

            // create token
            $token      = $user->createToken($credentials['username'])->accessToken;
            $type       = 'Bearer';
            $expires_in = $user->tokens->first()->expires_at->timestamp;

            return \App\Helpers\ApiResponse::withToken(true, $token, [
                'token_type'    => $type,
                'expires_in'    => $expires_in,
            ]);
        } else {
            return \App\Helpers\ApiResponse::error('User not found', 'unauthorized', null, 401);
        }
    }

    public function logout()
    {
        // laravel passport revokes the token
        $user = \Illuminate\Support\Facades\Auth::user();

        if (!$user) {
            return \App\Helpers\ApiResponse::error('User not found', 'unauthorized', null, 401);
        }

        $user->token()->revoke();

        return \App\Helpers\ApiResponse::success('User logged out successfully');
    }

    public function detail(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        return new \App\Http\Resources\User\Auth\DetailResource($user);
    }
}
