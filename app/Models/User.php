<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

use Illuminate\Support\Facades\DB;

/**
 * App\Models\User
 *
 * @property string $id_user
 * @property string $password

 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Client> $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Token> $tokens
 * @property-read int|null $tokens_count
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user';

    protected $primaryKey = 'id_user';

    protected $keyType = 'string';

    public $timestamps = false;

    public $incrementing = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    /**
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return \App\Models\User
     */
    public function findAndValidateForPassport($username, $password)
    {
        $query = $this->newModelQuery();

        $query->select(DB::raw('AES_DECRYPT(id_user, "' . env('MYSQL_AES_KEY_IDUSER') . '") as id_user'))
            ->where('id_user', DB::raw('AES_ENCRYPT("' . $username . '", "' . env('MYSQL_AES_KEY_IDUSER') . '")'))
            ->where('password', DB::raw('AES_ENCRYPT("' . $password . '", "' . env('MYSQL_AES_KEY_PASSWORD') . '")'));

        return $query->first();
    }

    /**
     * Specifies the user's FCM tokens
     *
     * @return string|array
     */
    public function routeNotificationForFcm()
    {
        return null;
    }

    public function createCustomToken(string $name, $clientId,  $scopes = [])
    {
        $token = $this->createToken($name, $scopes);

        // TODO : sesuaikan clientID dengan client yang ada di database
        if ($clientId == "mobile") {
            $token->token->expires_at = now()->addHour(6);
        } else {
            $token->token->expires_a = now()->addDays(30);
        }
    }

    /**
     * Get the user's detail
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * */
    public function detail()
    {
        return $this->hasOne(Pegawai::class, 'nik', 'id_user');
    }
}
