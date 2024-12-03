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
    Route::put('update', [SchemesController::class, 'updateScheme']);
    Route::get('get', [SchemesController::class, 'getSingleScheme']);
    Route::get('', [SchemesController::class, 'getAllSchemes']);
    Route::put('approve/{id}', [SchemesController::class, 'approveScheme']);
    Route::put('disable/{id}', [SchemesController::class, 'disableScheme']);
    Route::put('softDelete/{id}', [SchemesController::class, 'softDeleteScheme']);
    Route::put('permanentlyDelete/{id}', [SchemesController::class, 'permanentDeleteScheme']);

});