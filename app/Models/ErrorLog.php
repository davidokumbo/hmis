<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    use HasFactory;

    protected $table = "error_log";

    public $fillable = [
        'class',
        'method_and_line_number',
        'error_description',
        'related_user',
        'related_user_ip',
    ];

    //ensuring retionship with user is maintained during selection
    public function user(){
        return $this->belongsTo(User::class, 'related_user');
    }


    //satic function associated with this model
    public static function createErrorLog($class, $method_and_line_number, $error_description, $related_user, $related_user_ip){
        ErrorLog::create([
            'class'=>$class,
            'method_and_line_number'=>$method_and_line_number,
            'error_description'=>$error_description,
            'related_user'=>$related_user,
            'related_user_ip'=>$related_user_ip,
        ]);
    }

    public static function getErrorLogs($class, $method_and_line_number, $error_description, $related_user, $related_user_ip){
        $query = ErrorLog::select('error_log.id', 'error_log.class', 'error_log.method_and_line_number', 'error_log.error_description', 'error_log.related_user_ip')
                        ->with(['user' => function ($query) {
                            $query->select('id as user_id', 'email as user_email');
                        }]);
        
        
        if(!is_null($class)){
            $query->where("error_log.class", $class);
        }

        if(!is_null($method_and_line_number)){
            $query->where("error_log.method_and_line_number", "LIKE", $method_and_line_number.'%');
        }

        if(!is_null($error_description)){
            $query->where("user.email", $related_user)
                    ->orWhere("user.id", $related_user);
        }

        if(!is_null($related_user_ip)){
            $query->where("error_log.related_user_ip", $related_user_ip);
        }

        return $query->orderBy('error_log.created_at', "DESC")
                    ->get()                    
                    ->map(function ($log) {
                        $logArray = $log->toArray(); // Convert the log to an array
                        $user = $logArray['user'] ?? ['user_email'=>null]; // Get the user array or an empty array if no user exists
                        unset($logArray['user']); // Remove the nested user key
                        return array_merge($logArray, $user); // Merge the user data into the parent array;
                    });

    }
}