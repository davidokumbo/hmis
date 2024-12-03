<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Utils\APIConstants;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class BranchController extends Controller
{
    public function index(){
        try{
            $all = Branch::where('deleted_by', null)
                ->where('deleted_at', null)
                ->select('id', 'name', 'active', 'deleted_by', 'deleted_at')
                ->get();

            UserActivityLog::createUserActivityLog(APIConstants::NAME_GET, "Fetched all branches");

            return response()->json($all, 200);
        }
        catch(\Exception $e){
            return response()->json(['error'=>$e->getMessage()], 500);
        }
    }
    // create a branch
    public function store(Request $request){
        try{
            $request->validate([
                'name' => 'required|string|min:3|max:255|unique:branches,name',
                'active' => 'required|boolean|',
            ]);

            DB::beginTransaction();
            $branch = Branch::create([
                'name' => $request->name,
                'active'=>$request->active,
            ]);
    
            DB::commit();

            UserActivityLog::createUserActivityLog(APIConstants::NAME_CREATE, "Created a brach with name: ". $request->name);

            return response()->json([
                "id"=> $branch->id,
                "name"=> $branch->name,
                "active"=> $branch->active,
            ] ,200);
        }
        catch(ValidationException $e){
            return response()->json(['validation_error'=>$e->getMessage()], 422);
        }

        catch(\Exception $e){
            DB::rollBack();
            return response()->json(['error'=>$e->getMessage()], 500);
        }

        
    }

    // show branch details using id
    public function show(Request $request){
        try{
            $request->validate([
                'id' => 'required|integer|exists:branches,id',
            ]);

            $all = Branch::where('id', $request->id)
                ->select('id', 'name', 'active', 'deleted_by', 'deleted_at')
                ->get();

            
            UserActivityLog::createUserActivityLog(APIConstants::NAME_GET, "Fetched all branches");

            return response()->json($all, 200);

        }
        catch(ValidationException $e){
            return response()->json(['validation_error'=>$e->getMessage()], 422);
        }

        catch(\Exception $e){
            return response()->json(['error'=>$e->getMessage()], 500);
        }

    }

    // update branch details
    public function update(Request $request){
        try{
            $request->validate([
                'id' => 'required|integer|exists:branches,id',
                'name' => 'required|string|min:3|max:255',
                'active' => 'required|boolean|',
                'deleted_by' => 'integer|exists:users,id',
            ]);

            $branches = Branch::where('name', $request->name)->get('id');

            if((count($branches ) > 0) && ($branches[0]->id != $request->id)){
                return response()->json(['validation_error'=>"Branch with similar name already exists"], 422);
            }

            DB::beginTransaction();
            Branch::where('id', $request->id)
                ->update([
                    'name'=>$request->name, 
                    'active'=>$request->active,
                ]);

            DB::commit();

            UserActivityLog::createUserActivityLog(APIConstants::NAME_UPDATE, "Updated a branch with id: ". $request->id);

            return response()->json([
                "id"=>$request->id,
                "name"=>$request->name,
                "active"=>$request->active,
                "message"=>"success",
            ], 200);

        }
        catch(ValidationException $e){
            return response()->json(['validation_error'=>$e->getMessage()], 422);
        }

        catch(\Exception $e){
            DB::rollBack();
            return response()->json(['error'=>$e->getMessage()], 500);
        }

    }

    //deleting a branch, not a permanent delete
    public function destroy(Request $request){
        try{
            $request->validate([
                'id' => 'required|integer|exists:branches,id',
            ]);

            $all = Branch::where('id', $request->id)
                ->where('active', 0)
                ->whereNotNull('deleted_by')
                ->whereNotNull('deleted_at')
                ->get();

            if(count($all) > 0){
                return response()->json(['validation_error'=>"Branch already deleted"], 422);
            }
            
            DB::beginTransaction();
            $date = Carbon::now()->format('Y-m-d');
            Branch::where('id', $request->id)
                ->update([
                    'active'=>0, 
                    'deleted_by'=>User::getUserId(), 
                    'deleted_at'=>$date,
                ]);

            DB::commit();
            
            UserActivityLog::createUserActivityLog(APIConstants::NAME_DISABLE, "Disabled a branch with id: ". $request->id);

            return response()->json([
                "id"=>$request->id, 
                'deleted_by'=>User::getUserId(), 
                'deleted_at'=>$date,
            ], 200);
            
        }
        

        catch(ValidationException $e){
            return response()->json(['validation_error'=>$e->getMessage()], 422);
        }

        catch(\Exception $e){
            DB::rollBack();
            return response()->json(['error'=>$e->getMessage()], 500);
        }
    }

    public function getBranchesAndRoles(){
        try{
            $branches = Branch::where('active',1)
                ->where('deleted_by', null)
                ->where('deleted_at', null)
                ->select('id','name', 'active')
                ->get();

            $roles = Role::select('name')
                ->get();

            UserActivityLog::createUserActivityLog(APIConstants::NAME_GET, "Getting branches and roles: ");

            return response()->json([
                "branches"=>$branches,
                "roles"=>$roles,
            ], 200);
        }
        catch(\Exception $e){
            return response()->json(['error'=>$e->getMessage()], 500);  
        }
    }
}
