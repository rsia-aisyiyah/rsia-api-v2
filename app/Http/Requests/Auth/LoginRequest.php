<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'email'    => ['required', 'string', 'email'],
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
        $this->ensureIsNotRateLimited();

        $user = $this->getUserData();

        if (!$user) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'username' => trans('auth.failed'),
            ]);
        }

        unset($user->password);

        // Attempt authentication
        $attempt = Auth::loginUsingId($user->id_user);
        if (!$attempt) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'username' => trans('auth.failed'),
            ]);
        }

        Auth::setUser($user);

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('username')) . '|' . $this->ip();
    }

    public function getUserData()
    {
        $user = \App\Models\User::select(
            \Illuminate\Support\Facades\DB::raw('AES_DECRYPT(id_user, "' . env('MYSQL_AES_KEY_IDUSER') . '") as id_user'),
            \Illuminate\Support\Facades\DB::raw('AES_DECRYPT(id_user, "' . env('MYSQL_AES_KEY_IDUSER') . '") as username'),
            \Illuminate\Support\Facades\DB::raw('AES_DECRYPT(password, "' . env('MYSQL_AES_KEY_PASSWORD') . '") as password')
        )
            ->with(['detail' => function ($q) {
                $q->with('dep')->select('nik', 'nama', 'jbtn', 'departemen', 'bidang');
            }])
            ->where('id_user', \Illuminate\Support\Facades\DB::raw('AES_ENCRYPT("' . $this->input('username') . '", "' . env('MYSQL_AES_KEY_IDUSER') . '")'))
            ->where('password', \Illuminate\Support\Facades\DB::raw('AES_ENCRYPT("' . $this->input('password') . '", "' . env('MYSQL_AES_KEY_PASSWORD') . '")'))
            ->first();

        return $user;
    }
}
