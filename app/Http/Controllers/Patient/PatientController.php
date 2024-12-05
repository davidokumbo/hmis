<?php

namespace App\Http\Controllers\Patient;

use App\Exceptions\AlreadyExistsException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient\Patient;
use Illuminate\Support\Facades\Auth;
use App\Models\UserActivityLog;
use App\Utils\APIConstants;
use App\Exceptions\InputsValidationException;
use App\Exceptions\NotFoundException;
use App\Models\User;
use Carbon\Carbon;

class PatientController extends Controller
{

    //saving a new patient
    public function createPatient(Request $request){
        $request->validate([
            'firstname' => 'required|string|min:2|max:30',
            'lastname'=>'required|string|min:2|max:255',
            'dob' => 'required|date|before:today',
            'phonenumber1' => 'required|string|min:12|max:20|regex:/^\+?[0-9]{10,20}$/',
            'phonenumber2' => 'string|min:12|max:20|regex:/^\+?[0-9]{10,20}$/',
            'email' => 'required|string|min:5|max:50',
            'address' => 'required|string|min:3|max:50',
            'residence' => 'required|string|min:3|max:50'
            
        ]);
            

        Patient::create([
            'firstname' => $request->firstname, 
            'lastname' => $request->lastname,
            'phonenumber1'=>$request->phonenumber1,
            'phonenumber2'=>$request->phonenumber2, 
            'email' => $request->email,
            'dob' => $request->dob,
            'address'=>$request->address,
            'residence'=>$request->residence,
            'created_by' => Auth::user()->id
        ]);

        UserActivityLog::createUserActivityLog(APIConstants::NAME_CREATE, "Created a patient with name: ". $request->firstname . $request->lastname);

        return response()->json(
            Patient::selectPatients(null, $request->email)
        ,200);

    }

   // updating a patient
    public function updatePatient(Request $request){
        $request->validate([
            'firstname' => 'string|min:2|max:30',
            'lastname'=>'string|min:2|max:255',
            'dob' => 'date|before:today',
            'phonenumber1' => 'required|string|min:12|max:20|regex:/^\+?[0-9]{10,20}$/',
            'phonenumber2' => 'string|min:12|max:20|regex:/^\+?[0-9]{10,20}$/',
            'address' => 'string|min:3|max:50',
            'residence' => 'string|min:3|max:50' 
        ]);

        $existing = Patient::selectPatients(null, $request->email);

        if(count($existing) > 0 && $existing[0]["email"] != $request->email){
            throw new AlreadyExistsException(APIConstants::NAME_PATIENT. " ". $request->email);
        }

        Patient::where('email', $request->email)
                ->update([
                    'firstname' => $request->firstname, 
                    'lastname' => $request->lastname,
                    'phonenumber1'=>$request->phonenumber1,
                    'phonenumber2'=>$request->phonenumber2,
                    'dob' => $request->dob,
                    'address'=>$request->address,
                    'residence'=>$request->residence,
                    'updated_by' => User::getLoggedInUserId()
                ]);

        UserActivityLog::createUserActivityLog(APIConstants::NAME_UPDATE, "Updated a patient with email: ". $request->email);
        

        return response()->json(
            Patient::selectPatients(null, $request->email)
            ,200);

    }
    //Gettind a single patients details 
    public function getSinglePatient(Request $request){

        if($request->id == null && $request->email == null){
            throw new InputsValidationException("id or email required!");
        }

        $patient = Patient::selectPatients($request->id, $request->email);

        if(count($patient) < 1){
            throw new NotFoundException(APIConstants::NAME_PATIENT);
        }

        UserActivityLog::createUserActivityLog(APIConstants::NAME_GET, "Fetched a patient with id: ". $patient[0]['id']);

        return response()->json(
            $patient
        ,200);
    }
    //getting all patients Details
    public function getAllPatients(){

        $patients = Patient::selectPatients(null, null);

        UserActivityLog::createUserActivityLog(APIConstants::NAME_GET, "Fetched all patients");


        return response()->json(
            $patients
        ,200);
    }

    //approving a patient
    public function approvePatient($id){
            
        $existing = Patient::selectPatients($id, null);

        if(count($existing) < 1){
            throw new NotFoundException(APIConstants::NAME_PATIENT. " with id: ". $id);
        }

        $PatientNotDeleted = Patient::where('id', $id)
                                     ->whereNull('deleted_by')
                                     ->get();
        if ($PatientNotDeleted->isEmpty()) {
        throw new NotFoundException(APIConstants::NAME_PATIENT. " with id: ". $id . " was deleted.");
        }

        Patient::where('id', $id)
                ->update([
                    'approved_by' => User::getLoggedInUserId(),  
                    'approved_at' => Carbon::now(),
                    'disabled_by' => null,
                    'disabled_at' => null,
                ]);

        UserActivityLog::createUserActivityLog(APIConstants::NAME_APPROVE, "Approved a patient with id : ". $id);

        return response()->json(
            Patient::selectPatients($id, null)
        ,200);
    }

    // Disabling a patient
    public function disablePatient($id){
            
        $existing = Patient::selectPatients($id, null);

        if(count($existing) < 1){
            throw new NotFoundException(APIConstants::NAME_PATIENT. " with id: ". $id);
        }

        $PatientnotDeleted = Patient::where('id', $id)
                                   ->whereNull('deleted_by')
                                   ->get();
        if ($PatientnotDeleted->isEmpty()) {
            throw new NotFoundException(APIConstants::NAME_PATIENT. " with id: ". $id . " was deleted.");
        }
        
        Patient::where('id', $id)
                ->update([
                    'approved_by' => null, 
                    'approved_at' => null,
                    'disabled_by' => User::getLoggedInUserId(),
                    'disabled_at' => Carbon::now(),
                ]);


        UserActivityLog::createUserActivityLog(APIConstants::NAME_DISABLE, "Disabled a patient with id: ". $id);

        return response()->json(
            Patient::selectPatients($id, null, null)
        ,200);
    }

    public function deletePatient($id){
            
        $existing = Patient::selectPatients($id, null);

        if(count($existing) < 1){
            throw new NotFoundException(APIConstants::NAME_PATIENT. " with id: ". $id);
        }

        $PatientnotDeleted = Patient::where('id', $id)
                                   ->whereNull('deleted_by')
                                   ->get();
        if ($PatientnotDeleted->isEmpty()) {
            throw new NotFoundException(APIConstants::NAME_PATIENT. " with id: ". $id . "was deleted.");
        }
        
        Patient::where('id', $id)
                ->update([
                    'deleted_at' => now(),
                    'deleted_by' => User::getLoggedInUserId(),
                ]);


        UserActivityLog::createUserActivityLog(APIConstants::NAME_DISABLE, "Deleted a patient with id: ". $id);

        return response()->json(
            Patient::selectPatients($id, null, null)
        ,200);
    }
}
