<?php
use App\Http\Controllers\Logs\LogsController;
use Illuminate\Support\Facades\Route;

Route::get('auditLogs', [LogsController::class, 'getAuditLogs']);