<?php

use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'departments'], function(){

    Route::post('create', [DepartmentController::class, 'createDepartment']);
    Route::put('update', [DepartmentController::class, 'updateDepartment']);
    Route::get('get', [DepartmentController::class, 'getSingleDepartment']);
    Route::get('', [DepartmentController::class, 'getAllDepartments']);
    Route::put('approve/{id}', [DepartmentController::class, 'approveDepartment']);
    Route::put('disable/{id}', [DepartmentController::class, 'disableDepartment']);
});

Route::group(['prefix'=>'employees'], function(){
    Route::post('create', [EmployeeController::class, 'createEmployee']);
    Route::put('update', [EmployeeController::class, 'updateEmployee']);
    Route::get('get', [EmployeeController::class, 'getSingleEmployee']);
    Route::get('', [EmployeeController::class, 'getAllEmployees']);
    Route::put('approve/{id}', [EmployeeController::class, 'approveEmployee']);
    Route::put('disable/{id}', [EmployeeController::class, 'disableEmployee']);;

//schemes routes 
Route::group(['prefix'=>'schemes'], function(){

    Route::post('create', [SchemesController::class, 'createScheme']);
    Route::put('update', [SchemesController::class, 'updateScheme']);
    Route::get('get', [SchemesController::class, 'getSingleScheme']);
    Route::get('', [SchemesController::class, 'getAllSchemes']);
    Route::put('approve/{id}', [SchemesController::class, 'approveScheme']);
    Route::put('disable/{id}', [SchemesController::class, 'disableScheme']);
    Route::put('softDelete/{id}', [SchemesController::class, 'softDeleteScheme']);
    Route::delete('permanentlyDelete/{id}', [SchemesController::class, 'permanentDeleteScheme']);

});