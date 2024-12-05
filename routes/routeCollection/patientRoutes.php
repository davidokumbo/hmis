<?php

use App\Http\Controllers\Patient\PatientController;

Route::group(['prefix'=>'patients'], function(){
    Route::post('create', [PatientController::class, 'createPatient']);
    Route::put('update', [PatientController::class, 'updatePatient']);
    Route::get('get', [PatientController::class, 'getSinglePatient']);
    Route::get('', [PatientController::class, 'getAllPatients']);
    Route::put('approve/{id}', [PatientController::class, 'approvePatient']);
    Route::put('disable/{id}', [PatientController::class, 'disablePatient']);
    Route::put('delete/{id}', [PatientController::class, 'deletePatient']);
});