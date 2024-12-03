<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\AlreadyExistsException;
use App\Exceptions\InputsValidationException;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Models\Admin\Department;
use App\Models\User;
use App\Utils\APIConstants;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function createDepartment(Request $request){
        $request->validate([
            'name' => 'required|string|min:3|max:255|unique:departments'
        ]);

        // // This is a test logic to show how you can throw a sample error with a message, check how the
        // // class is created and hanled in the handler class....
        // //this logic is not needed because it is already checked in above validation
        // $existing = Department::selectDepartments(null, $request->name);
        // if(count($existing) > 0){
        //     throw new AlreadyExistsException(APIConstants::NAME_DEPARTMENT);
        // }

        Department::create([
            'name' => $request->name,
            'created_by' => Auth::user()->id
        ]);

        return response()->json(
            Department::selectDepartments(null, $request->name)
        ,200);
    }

    public function updateDepartment(Request $request){
        $request->validate([
            'id' => 'required|integer|min:1|exists:departments,id',
            'name' => 'required|string|min:3|max:255'
        ]);

        $existing = Department::selectDepartments(null, $request->name);

        if(count($existing) > 0 && $existing[0]["id"] != $request->id){
            throw new AlreadyExistsException(APIConstants::NAME_DEPARTMENT. " ". $request->name);
        }

        Department::where('id', $request->id)
                ->update([
                    'name'=>$request->name, 
                    'updated_by' => User::getLoggedInUserId()
                ]);

        return response()->json(
            Department::selectDepartments($request->id, $request->name)
        ,200);

    }

    public function getSingleDepartment(Request $request){

        if($request->id == null && $request->name == null){
            throw new InputsValidationException("id or name required!");
        }

        return response()->json(
            Department::selectDepartments($request->id, $request->name)
        ,200);
    }

    public function getAllDepartments(){
        return response()->json(
            Department::selectDepartments(null, null)
        ,200);
    }

    public function approveDepartment($id){
        
        $existing = Department::selectDepartments($id, null);

        if(count($existing) < 1){
            throw new NotFoundException(APIConstants::NAME_DEPARTMENT. " with id: ". $id);
        }

        Department::where('id', $id)
                ->update([
                    'approved_by' => User::getLoggedInUserId(), 
                    'approved_at' => Carbon::now(),
                    'disabled_by' => null,
                    'disabled_at' => null,
                ]);

        return response()->json(
            Department::selectDepartments($id, null)
        ,200);
    }

    public function disableDepartment($id){
        
        $existing = Department::selectDepartments($id, null);

        if(count($existing) < 1){
            throw new NotFoundException(APIConstants::NAME_DEPARTMENT. " with id: ". $id);
        }

        Department::where('id', $id)
                ->update([
                    'approved_by' => null, 
                    'approved_at' => null,
                    'disabled_by' => User::getLoggedInUserId(),
                    'disabled_at' => Carbon::now(),
                ]);

        return response()->json(
            Department::selectDepartments($id, null)
        ,200);
    }
}
