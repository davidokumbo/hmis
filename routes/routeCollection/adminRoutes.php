<?php

use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\SchemesController;
use Illuminate\Support\Facades\Route;

//department routes
Route::group(['prefix'=>'departments'], function(){

    Route::post('create', [DepartmentController::class, 'createDepartment']);
    Route::put('update', [DepartmentController::class, 'updateDepartment']);
    Route::get('get', [DepartmentController::class, 'getSingleDepartment']);
    Route::get('', [DepartmentController::class, 'getAllDepartments']);
    Route::put('approve/{id}', [DepartmentController::class, 'approveDepartment']);
    Route::put('disable/{id}', [DepartmentController::class, 'disableDepartment']);

});


//schemes routes 
Route::group(['prefix'=>'schemes'], function(){

    Route::post('create', [SchemesController::class, 'createScheme']);
    // Route::put('update', [DepartmentController::class, 'updateDepartment']);
    // Route::get('get', [DepartmentController::class, 'getSingleDepartment']);
    // Route::get('', [DepartmentController::class, 'getAllDepartments']);
    // Route::put('approve/{id}', [DepartmentController::class, 'approveDepartment']);
    // Route::put('disable/{id}', [DepartmentController::class, 'disableDepartment']);

});