<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    use HasFactory;

    protected $table = "user_activity_log";

    protected $fillable = [
        'operation_type',
        'description',
        'user_id',
        'is_logged_in',
        'ip_address'
    ];


    //Ensuring relationship during selection
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function createUserActivityLog($operation_type, $description){
        $user_id = User::getLoggedInUserId();
        $is_logged_in = false;
        if($user_id){
            $is_logged_in = true;
        }

        UserActivityLog::create([
            'operation_type' => $operation_type,
            'description' => $description,
            'user_id' => $user_id,
            'is_logged_in' => $is_logged_in,
            'ip_address' => request()->ip()
        ]);
    }


    public static function getUserActivityLogs($operation_type, $description, $user_id, $is_logged_in, $ip_address){
        $query = UserActivityLog::select('user_activity_log.id', 'user_activity_log.operation_type', 'user_activity_log.description', 'user_activity_log.is_logged_in', 'user_activity_log.ip_address', 'user_activity_log.user_id')
                        ->with(['user' => function ($query) {
                            $query->select('users.id', 'users.email');
                        }]); // Load only the 'id' and 'email' from the related user

        if($operation_type){
            $query->where('user_activity_log.operation_type', $operation_type);
        }        
        
        if($description){
            $query->where('user_activity_log.description', $description);
        }

        if(!is_null($user_id)){
            $query->where("user.id", $user_id)
                    ->orWhere('user.email', $user_id);
        }

        if(!is_null($is_logged_in)){
            $query->where('user_activity_log.is_logged_in', $is_logged_in);
        }
        
        if(!is_null($ip_address)){
            $query->where('user_activity_log.ip_address', $ip_address);
        }

        return $query->get()                    
                    ->map(function ($log) {
                        $logArray = $log->toArray();
                        $user = $logArray['user'] ?? ['user_id'=>null, 'user_email'=>null];
                        $userTransformed = [
                            'original_user_id' => $user['id'],
                            'user_email' => $user['email']
                        ];
                        unset($logArray['user']);
                        return array_merge($logArray, $userTransformed);
                    });
    }


}
