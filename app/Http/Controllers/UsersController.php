<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\user;
use App\Models\department;

class UsersController extends Controller
{

	function view_records(Request $req){

        $validator = Validator::make($req->all(),[
            "limit" => "numeric",
        ]);

        if($validator->Fails()){
            return response()->json(["status" => "Validation Fails", "errors" => $validator->errors()], 422);
        }

        try{
        //    $limit = 10;
            $orderby = 'modified_on';
         //  if($req->limit) $limit = $req->limit;
           $limit = $req->input('limit',10);

		    $details = user::join("departments","departments.id","=","users.department_id");

            // Search by column name and searchtext
            if($req->searchtext){
                $details = $details->where('firstname',"like",'%'.$req->searchtext.'%');
                $details = $details->orwhere('lastname',"like",'%'.$req->searchtext.'%');
                $details = $details->orwhere('email',"like",'%'.$req->searchtext.'%');
            }

            //Order by desc
            if($req->orderby) $orderby = $req->orderby;
            $details = $details->OrderByDesc($orderby);


            $details = $details->paginate($limit,['users.id','users.firstname','users.lastname','users.email','users.dob','users.salary','departments.name']);
	    	return response()->json([
    			"status" => "Records Found",
			    "Details" => $details
		    ], 201);
        }catch(\Exception $e){
            return response()->json([
                "error" => $e->getMessage()
            ], 422);
        }
     }

	function add_record(Request $req){
		
		$validator = Validator::make($req->all(),[
            "firstname" => "required|min:2|max:50",
            "lastname" => "required|min:2|max:50",
            "email" => "required|email|unique:users|email:rfc,dns",
            "dob" => "required|date|date_format:Y-m-d",
            "salary" => "required|numeric|min:2",
            "department" => "required|min:2|max:40",
            "id" => "required|numeric"
        ]);

        if($validator->Fails()){
                return response()->json(["status" => "Validation Fails","errors" => $validator->errors()], 422);
        };

		try{
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
		}catch(\Exception $e){
			return response()->json(["errors" => $e->getMessage()], 422);
		}
	}



	function update_record(Request $req)
    {

        $validator = Validator::make($req->all(),[
            "firstname" => "required|min:2|max:50",
            "lastname" => "required|min:2|max:50",
            "email" => "required|email|unique:users|email:rfc,dns",
            "dob" => "required|date|date_format:Y-m-d",
            "salary" => "required|numeric|min:2",
            "department" => "required|min:2|max:40",
            "id" => "required|numeric"
        ]);

        if($validator->Fails()){
                return response()->json(["status" => "Validation Fails","errors" => $validator->errors()], 422);
        };

		try{
			if($req->id == 0){
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

				return response()->json(["status" => "user created successfully", "user" => $user, "department" => $department], 201);
			}else{
				$user = user::find($req->id);
				if($user){
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
				}else{
					return response()->json(["status"=>"Entered userid is not valid"], 422);
				}
			}
		}catch(\Exception $e){
				return response()->json(["errors" => $e->getMessage()], 422);

		}
	}

	
	function delete_record(Request $req){

        $validator = Validator::make($req->all(),[
            "id" => "required|numeric|min:1"
        ]);

        if($validator->Fails()){
                return response()->json(["status" => "Validation Fails","errors" => $validator->errors()], 422);
        };

        try{
		    $user = user::find($req->id);
            if($user){
                $user->delete();
       
	    	    return response()->json(["status" => "User record deleted successfully.", "Details" => $user], 201);
           }else{
                return response()->json(["Message" => "Invalid User Id passed"], 422);
            }
        }catch(\Exception $e){
            return response()->json(["errors" => $e->getMessage()], 422);
        }
	}

    function view_single_records($id){
        try{
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
        }catch(\Exception $e){
            return response()->json(["errors" => $e->getMessage()], 422);
        }
        
    }

}
