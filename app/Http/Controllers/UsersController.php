<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;

class UsersController extends Controller
{

	function ViewRecords(Request $request)
    {

        $validator = Validator::make($request->all(),[
            "limit" => "numeric",
        ]);

        if($validator->Fails()){
            return response()->json(["status" => "Validation Fails", "errors" => $validator->errors()], 422);
        }

        try{
            $user = new User;
            $view = $user->UserRecords($request);
            if($view){
                return response()->json(["status" => "Records Found","Details" => $view], 201);
            }else {
                return response()->json(["status"=>"Records not Found"], 422);
            }
        }catch(\Exception $e){
            return response()->json(["error" => $e->getMessage()], 422);
        }
     }

	function UpdateRecord(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "first_name" => "required|min:2|max:50",
            "last_name" => "required|min:2|max:50",
            "email" => "required|email|unique:users|email:rfc,dns",
            "dob" => "required|date|date_format:Y-m-d",
            "salary" => "required|numeric|min:2",
            "department_id" => "numeric|min:1",
            "id" => "required|numeric"
        ]);
        if($validator->Fails()){
                return response()->json(["status" => "Validation Fails","errors" => $validator->errors()], 422);
        };
		try{
			if($request->id == 0){
				$user = new User;
                $userdetails = $user->CreateRecord($request);
                if($userdetails){
        		    return response()->json(["status" => "user created successfully", "user" => $userdetails], 201);
                }else{
                    return response()->json(["status" => "Error in user creation", "user" => $userdetails], 201);
                }
            }
            $user = new User;
            $userdetails = $user->UpdateUserRecord($request);
            if($userdetails){
				return response()->json(["status" => "User updated successfully","user" => $userdetails], 201);
            }else{
                return response()->json(["status"=>"Entered userid is not valid"], 422);
            }
		}catch(\Exception $e){
				return response()->json(["errors" => $e->getMessage()], 422);
		}
	}

	
	function DeleteRecord(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "id" => "required|numeric|min:1"
        ]);
        if($validator->Fails()){
                return response()->json(["status" => "Validation Fails","errors" => $validator->errors()], 422);
        };
        try{
            $user = new User;
            $deleteduser = $user->DeleteUser($request);
            if($deleteduser){       
	    	    return response()->json(["status" => "User record deleted successfully.", "Details" => $deleteduser], 201);
            }else{
                return response()->json(["Message" => "Invalid User Id passed"], 422);
            }
        }catch(\Exception $e){
            return response()->json(["errors" => $e->getMessage()], 422);
        }
	}

    function ViewSingleRecords($id)
    {
        try{
            $user = new User;
            $viewuser = $user->ViewUser($id);
            if($viewuser){
               return response()->json(["status" => "User record found","Details" => $viewuser], 201);
            }else{
               return response()->json(["status" => "User record not found"], 422);
            }
        }catch(\Exception $e){
            return response()->json(["errors" => $e->getMessage()], 422);
        }   
    }
}
