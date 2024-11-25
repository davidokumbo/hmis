<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ErrorLog;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    public function getUserActivityLogs(Request $request){

        UserActivityLog::createUserActivityLog("READING", "CHECKING USER ACTIVITY LOGS");
        
        return response()->json(UserActivityLog::getUserActivityLogs($request->operation_type, $request->description, $request->user_id, $request->is_logged_in, $request->ip_address), 200);
    }
    //get All Audit Logs
    public function getAuditLogs(Request $request){
        //test audit log creation
        //AuditLog::createAuditLog("TEST", "THIS IS A TEST LOG", User::getLoggedInUserId());
        return response()->json(AuditLog::getAuditLogs($request->user, $request->action), 200);
    }

    public function getErrorLogs(Request $request){
        return response()->json(ErrorLog::getErrorLogs($request->class, $request->method, $request->error_description, $request->related_user, $request->related_user_ip));
    }
}
