<?php

use App\Http\Controllers\Admin\DepartmentController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'departments'], function(){

    Route::get('create', [DepartmentController::class, 'createDepartment']);
});