<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\user;
use App\Models\department;

class UsersController extends Controller
{

	function view_records(){
		$details = user::join("departments","departments.id","=","users.department_id")->paginate(5,['users.id','users.firstname','users.lastname','users.email','users.dob','users.salary','departments.name']);
		return response()->json([
			"status" => "Records Found",
			"Details" => $details
		], 201);
	
	}

	function add_record(Request $req){
		$validator = Validator::make($req->all(), [
			'firstname' => 'required|min:2|max:100',
			'lastname' => 'required|min:2|max:100',
			'email' => 'required|email|unique:users',
			'dob' => 'required',
			'salary' => 'required|numeric|min:2',
			'department' => 'required|min:2|max:100'
		
		]);

		if($validator->fails()){
			return response()->json([
				"message" => "Validation Fails",
				"errors" => $validator->errors()
			], 422);
		}

		$department = new department;
		$department->name = $req->department;
		$department->save();


		$user = new user;
		$user->firstname = $req->firstname;
		$user->lastname = $req->lastname;
		$user->email = $req->email;
		$user->dob = $req->dob;
		$user->salary = $req->salary;
		$user->department_id = $department->id;
		$user->save();

		return response()->json([
			"status" => "User added successfully",
			"userdetails" => $user,
			"department" => $department
		], 201);
	}



	function update_record(Request $req){
		$user = user::find($req->id);
		$user->firstname = $req->firstname;
                $user->lastname = $req->lastname;
                $user->email = $req->email;
                $user->dob = $req->dob;
		$user->salary = $req->salary;
		$user->save();

		$department = department::find($user->department_id);
		$department->name = $req->department;
		$department->save();

		return response()->json([
			"status" => "User updated successfully",
			"user" => $user,
			"department" => $department
		], 201);
	}

	
	function delete_record(Request $req){
		$user = user::find($req->id);
		$user->delete();
		$department = department::find($user->department_id);
		$department->delete();

		return response()->json([
			"status" => "User record deleted successfully.",
			"Details" => $user
		], 201);
	}

    function view_single_records($id){
        $users = user::find($id);

        if($users){
            return response()->json([
                "status" => "User record found",
                "Details" => $users
            ], 201);
        }else{
            return response()->json([
                "status" => "User record not found"
            ], 422);
        }
        
    }

}
