<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    //get All Audit Logs
    public function getAuditLogs(Request $request){
        return response()->json(AuditLog::getAuditLogs($request->user, $request->action), 200);
    }
}
