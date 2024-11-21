<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = "audit_log";

    public static function createAuditLog($operation_type, $description, $related_user){

        AuditLog::create([
            'operation_type' => $operation_type,
            'description'=>$description,
            'related_user'=>$related_user,
        ]);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public static function getAuditLogs($user, $action){
        $query = AuditLog::select('id')
                    ->with(['user:id,email']); // Load only the 'id' and 'email' from the related user

        if(!is_null($user)){
            $query->where("user.id", $user)
                    ->orWhere('user.email', $user);
        }

        if(!is_null($action)){
            $query->where('audit_log.operation_type', $action)
                    ->orWhere('audit_log.description','LIKE', '%'.$action.'%');
        }

        return $query->get();
    }
}
