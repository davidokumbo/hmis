<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Models\UserActivityLog;
use App\Utils\APIConstants;
use App\Exceptions\InputsValidationException;
use App\Exceptions\NotFoundException;


class EmployeeController extends Controller
{

  public function createEmployee(Request $request){
    $request->validate([
        'employee_name' => 'required|string|min:2|max:255',
        'ipnumber'=>'required',
        'age' => 'required|string|min:1|max:3',
        'dob' => 'required|date|before:today',
        'speciality' => 'required|string|min:3|max:25',
        'role' => 'required|string|min:2|max:255|exists:roles,name'
    ]);
     
    DB::beginTransaction();

    $employeeCode = $this->generateEmployeeCode();
    $employee = Employee::create([
        'employee_name' => $request->employee_name, 
        'ipnumber'=>$request->ipnumber,
        'employee_code'=>$employeeCode, 
        'age' => $request->age,
        'dob' => $request->dob,
        'role'=>$request->role,
        'speciality' => $request->speciality,
        'user_id' => Auth::user()->id,
        'created_by' => Auth::user()->id,
        'approved_by'=>Auth::user()->id,
        'apdated_by'=>Auth::user()->id
    ]);
   
    DB::commit();
    UserActivityLog::createUserActivityLog(APIConstants::NAME_CREATE, "Created an employee with name: ". $request->employee_name);

    return response()->json(
        Employee::selectEmployees(null, $request->name)
    ,200);
  }

  //function to generate employeecode
  public function generateEmployeeCode()
  {
      // Generate a random six-digit number
      $randomNumber = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
  
      // Add the EMP prefix
      $employeeCode = 'EMP' . $randomNumber;
  
      // Check if the code already exists in the database
      while (Employee::where('employee_code', $employeeCode)->exists()) {
          $randomNumber = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
          $employeeCode = 'EMP' . $randomNumber;
      }
  
      return $employeeCode;
  }


//updating employer details
  public function updateEmployee(Request $request){
    $request->validate([
        'id' => 'required|integer|min:1|exists:employees,id',
        'ipnumber'=> 'required|integer|min:1|exists:employees,ipnumber',
        'employee_code'=> 'required|string|min:1|exists:employees,employee_code',
        'age' => 'required|integer|min:1|max:100',
        'dob'=> 'date:before:today',
        'role'=> 'string',
        'employee_name'=> 'string|min:1',
        'speciality'=> 'string|min:1'
    ]);

    $existing = Employee::selectEmployees(null, $request->id);

    if(count($existing) > 0 && $existing[0]["id"] != $request->id){
        throw new AlreadyExistsException(APIConstants::NAME_DEPARTMENT. " ". $request->id);
    }

    Employee::where('id', $request->id)
            ->update([
                'employee_name'=>$request->employee_name, 
                'age' =>$request->age,
                'dob'=>$request->dob,
                'role'=> $request->role,
                'speciality'=> $request->speciality,
                'updated_by' => User::getLoggedInUserId()
            ]);

    UserActivityLog::createUserActivityLog(APIConstants::NAME_UPDATE, "Updated an employee with id: ". $request->id);
    

    return response()->json(
        Employee::selectEmployees($request->id, $request->employee_name)
    ,200);

}

//getting single employee
public function getSingleEmployee(Request $request){

    if($request->id == null && $request->employee_code == null){
        throw new InputsValidationException("id or employee code required!");
    }

    $employee = Employee::selectEmployees($request->id, $request->employee_code);

    if(count($employee) < 1){
        throw new NotFoundException(APIConstants::NAME_EMPLOYEE);
    }

    UserActivityLog::createUserActivityLog(APIConstants::NAME_GET, "Fetched an employee with id: ". $employee[0]['id']);

    return response()->json(
        $employee
    ,200);
}

  

public function getAllEmployees(){

    $employees = Employee::selectEmployees(null, null);

    UserActivityLog::createUserActivityLog(APIConstants::NAME_GET, "Fetched all employees");


    return response()->json(
        $employees
    ,200);
}


public function approveEmployee($id){
        
    $existing = Employee::selectEmployees($id, null);

    if(count($existing) < 1){
        throw new NotFoundException(APIConstants::NAME_EMPLOYEE. " with id: ". $id);
    }

    createEmployee::where('id', $id)
            ->update([
                'approved_by' => User::getLoggedInUserId(), 
                'approved_at' => Carbon::now(),
                'disabled_by' => null,
                'disabled_at' => null,
            ]);

    UserActivityLog::createUserActivityLog(APIConstants::NAME_APPROVE, "Approved an Employee with id: ". $id);

    return response()->json(
        Department::selectEmployees($id, null)
    ,200);
}

public function disableEmployee($id){
        
    $existing = Department::selectEmployees($id, null);

    if(count($existing) < 1){
        throw new NotFoundException(APIConstants::NAME_EMPLOYEE. " with id: ". $id);
    }

    Department::where('id', $id)
            ->update([
                'approved_by' => null, 
                'approved_at' => null,
                'disabled_by' => User::getLoggedInUserId(),
                'disabled_at' => Carbon::now(),
            ]);

    UserActivityLog::createUserActivityLog(APIConstants::NAME_DISABLE, "Disabled an employee with id: ". $id);

    return response()->json(
        Employee::selectDepartments($id, null)
    ,200);
}


}
