<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = "audit_log";

    protected $fillable = [
        'operation_type',
        'description',
        'related_user'
    ];

    
    //ensuring related user is selected or pinned to the audit log 
    public function user(){
        return $this->belongsTo(User::class, 'related_user', 'id');
    }


    //Static function tied to this model
    public static function createAuditLog($operation_type, $description, $related_user){

        AuditLog::create([
            'operation_type' => $operation_type,
            'description'=>$description,
            'related_user'=>$related_user,
        ]);
    }

    public static function getAuditLogs($user, $action){
        $query = AuditLog::select('audit_log.id AS audit_id', 'audit_log.operation_type', 'audit_log.description', 'audit_log.description', 'audit_log.related_user', 'audit_log.created_at')
                        ->with(['user' => function ($query) {
                            $query->select('users.id', 'users.email');
                        }]); // Load only the 'id' and 'email' from the related user

        // $query = AuditLog::select('audit_log.id', 'audit_log.operation_type', 'audit_log.description', 'audit_log.description', 'audit_log.related_user')
        // ->with(['user' => function ($query) {
        //     $query->select('id', 'email');
        // }])->get();

        // echo($query);

        if(!is_null($user)){
            $query->where("user.id", $user)
                    ->orWhere('user.email', $user);
        }

        if(!is_null($action)){
            $query->where('audit_log.operation_type', $action)
                    ->orWhere('audit_log.description','LIKE', '%'.$action.'%');
        }

        return $query->get()
                    ->map(function ($log) {
                        $logArray = $log->toArray();
                        $user = $logArray['user'] ?? ['id'=>null, 'email'=>null];
                        $userTransformed = [
                            'user_id' => $user['id'],
                            'user_email' => $user['email']
                        ];
                        unset($logArray['user']);
                        return array_merge($logArray, $userTransformed);
                    });
                           
        
    }
}
