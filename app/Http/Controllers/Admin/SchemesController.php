<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Scheme;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Utils\APIConstants;
use Illuminate\Http\Request;

class SchemesController extends Controller
{
    //create a scheme
    public function createScheme(Request $request){
        $request->validate([
            'name' => 'required|string|min:3|max:255|unique:schemes',
            'account' => 'required|string|min:1|max:255|unique:schemes',
            'initiate_url' => 'string|min:3|max:255',
            'bill_url' => 'string|min:3|max:255',
            'authentication_url' => 'string|min:3|max:255',
            'validation_url' => 'string|min:3|max:255',
            'balance_url' => 'string|min:3|max:255',
            'bridge_balance_url' => 'string|min:3|max:255',
            'other_url' => 'string|min:3|max:255',
            'username' => 'string|min:3|max:255',
            'password' => 'string|min:3|max:255',
            'description' => 'string|max:1000',
        ]);

    
    Scheme::create([
        'name' => $request->name,
        'account' => $request->account,
        'initiate_url' => $request->initiate_url,
        'bill_url' => $request->bill_url,
        'authentication_url' => $request->authentication_url,
        'validation_url' => $request->validation_url,
        'balance_url' => $request->balance_url,
        'bridge_balance_url' => $request->bridge_balance_url,
        'other_url' => $request->other_url,
        'username' => $request->username,
        'password' => $request->password,
        'description' => $request->description,
        'created_by' => User::getLoggedInUserId()
    ]);

    UserActivityLog::createUserActivityLog(APIConstants::NAME_CREATE, "Created a Scheme with name: ". $request->name);

    return response()->json(
            Scheme::selectSchemes(null, $request->name)
        ,200);
    }
}
