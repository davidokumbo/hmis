<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(){
        $all = Branch::where('deleted_by', null)
            ->where('deleted_at', null)
            ->select('id', 'name', 'active', 'deleted_by', 'deleted_at')
            ->get();

        return response()->json($all, 200);
        
    }
    // create a branch
    public function store(Request $request){
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
        return response()->json([
            "id"=> $branch->id,
            "name"=> $branch->name,
            "active"=> $branch->active,
        ] ,200);
    

        
    }

    // show branch details using id
    public function show(Request $request){
        $request->validate([
            'id' => 'required|integer|exists:branches,id',
        ]);

        $all = Branch::where('id', $request->id)
            ->select('id', 'name', 'active', 'deleted_by', 'deleted_at')
            ->get();

        return response()->json($all, 200);


    }

    // update branch details
    public function update(Request $request){
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
        return response()->json([
            "id"=>$request->id,
            "name"=>$request->name,
            "active"=>$request->active,
            "message"=>"success",
        ], 200);

        

    }

    //deleting a branch, not a permanent delete
    public function destroy(Request $request){
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
        return response()->json([
            "id"=>$request->id, 
            'deleted_by'=>User::getUserId(), 
            'deleted_at'=>$date,
        ], 200);
        

    }
}
