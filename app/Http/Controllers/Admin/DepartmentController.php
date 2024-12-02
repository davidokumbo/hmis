<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\AlreadyExistsException;
use App\Http\Controllers\Controller;
use App\Models\Admin\Department;
use App\Utils\APIConstants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isEmpty;

class DepartmentController extends Controller
{
    public function createDepartment(Request $request){
        $request->validate([
            'name' => 'required|string|min:3|max:255|unique:departments'
        ]);

        // This is a test logic to show how you can throw a sample error with a message, check how the
        // class is created and hanled in the handler class....
        //this logic is not needed because it is already checked in above validation
        $existing = Department::selectDepartments(null, $request->name);
        if(count($existing) > 0){
            throw new AlreadyExistsException(APIConstants::NAME_DEPARTMENT);
        }

        Department::create([
            'name' => $request->name,
            'created_by' => Auth::user()->id
        ]);

        return response()->json(
            Department::selectDepartments(null, $request->name)
        ,200);
    }
}
