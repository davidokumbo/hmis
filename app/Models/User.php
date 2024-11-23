<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'branch_id',
        'active',
        'deleted_by',
        'deleted_at',        
    ];

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
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

     /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    // function to get user_id from jwttoken
    public static function getUserIdFromToken(){
        $token = JWTAuth::parseToken()->getPayload()->toArray();
        return $token['sub'];
    }
    
    //function to get all user details from users table
    public static function getUserFromToken(){
        return User::find(User::getUserIdFromToken());
    }

    public static function getLoggedInUser(){
        return Auth::user();
    }

    public static function getLoggedInUserEmail(){
        $user = User::getLoggedInUser();

        if($user){
            return $user->email;
        }

        return null;
    }

    public static function getLoggedInUserId(){
        $user = User::getLoggedInUser();

        if($user){
            return $user->id;
        }

        return null;
    }

    public static function getUserIp(){
        return request()->ip();
    }








    //Lets create the relationships
    public function auditLogs(){
        return $this->hasMany(AuditLog::class);
    }
}
