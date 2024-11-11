<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('test', [AuthController::class, 'test']);
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// authenticated routes to require jwt validation
Route::middleware('jwt.auth')->group(function(){

    //admin routes only
    Route::group(['middleware' => ['roles.check:hod,admin']], function(){
        Route::get('branches', [BranchController::class, 'index']);
        Route::post('branches', [BranchController::class, 'store']);
        Route::put('branches', [BranchController::class, 'update']);
        Route::delete('branches', [BranchController::class, 'destroy']);
        Route::get('getBranchesAndRoles', [BranchController::class, 'getBranchesAndRoles']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('register', [AuthController::class, 'register']);

    });
    
});
