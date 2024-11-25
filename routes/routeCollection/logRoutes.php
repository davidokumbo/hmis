<?php
use App\Http\Controllers\Logs\LogsController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'logs'], function(){

    Route::get('userActivityLogs', [LogsController::class, 'getUserActivityLogs']);
    Route::get('auditLogs', [LogsController::class, 'getAuditLogs']);
    Route::get('errorLogs', [LogsController::class, 'getErrorLogs']);
});