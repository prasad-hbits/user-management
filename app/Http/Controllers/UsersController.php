<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\Departments;
use Exception;

class UsersController extends Controller
{
    /**
     * Description - Api to get all Users record using paginate, search on table first_name, last_name, email, salary
     * default limit is 10, orderby paramenter is in json format consists of colname and sort i.e. Asc or Desc.
     * @author  Prasad
     */

	function viewRecords(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "limit" => "numeric",
            "orderby" => "json",
            "page" => "numeric|min:1"
        ]);
        if($validator->Fails()) return response()->json(["status" => false, "message" => $validator->errors()], config('constants.lang.validation_fail'));

        try {
            $view = Users::userRecords($request);
            if($view) {
                return response()->json(["status" => true, "message" => "Records Found","data" => $view], config('constants.lang.success'));
            } else {
                return response()->json(["status" => false, "message" => "Records not Found"], config('constants.lang.ok'));
            }
        } catch(Exception $e) {
            return response()->json(["status" => false, "message" => $e->getMessage()], config('constants.lang.server_error'));
        }
     }

    /**
     * Description - Api to create and update User record
     * @author Prasad
     */
	function updateRecord(Request $request)
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
        if($validator->Fails())  return response()->json(["status" => false, "message" => $validator->errors()], config('constants.lang.validation_fail'));
		
        try {
			if($request->id == 0) {
                Users::createRecord($request);
                return response()->json(["status" => true, "message" => "User created successfully"], config('constants.lang.success'));
            }
            if(Users::checkUserId($request->id) == false)  return response()->json(["status" => false, "message" => "Entered userid is not valid"], config('constants.lang.ok'));    
            Users::updateUserRecord($request);
            return response()->json(["status" => true, "message" => "User updated successfully"], config('constants.lang.success'));
        } catch(Exception $e) {
			return response()->json(["status" => false, "message" => $e->getMessage()], config('constants.lang.server_error'));
		}
	}

	/**
     * Description - Api to delete User record by its id.
     * @author Prasad
     */
	function deleteRecord(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "id" => "required|numeric|min:1"
        ]);
        if($validator->Fails())  return response()->json(["status" => false, "message" => $validator->errors()], config('constants.lang.validation_fail'));

        try {
            if(Users::checkUserId($request->id) == false) return response()->json(["status" => false, "message" => "Invalid User Id passed"], config('constants.lang.ok'));
            Users::deleteUser($request);    
            return response()->json(["status" => true, "message" => "User record deleted successfully."], config('constants.lang.success'));
        } catch(Exception $e) {
            return response()->json(["status" => false, "errors" => $e->getMessage()], config('constants.lang.server_error'));
        }
	}

    /**
     * Description - Api to view single User record  by its id.
     * @author  Prasad
     */
    function viewSingleRecords($id)
    {
        try {
            if(Users::checkUserId($id) == false)  return response()->json(["status" => false, "message" => "User record not found"], config('constants.lang.ok'));
            $viewuser = Users::viewUser($id);
            return response()->json(["status" => true, "message" => "User record found", "data" => $viewuser], config('constants.lang.success'));
        } catch(Exception $e) {
            return response()->json(["status" => false, "message" => $e->getMessage()], config('constants.lang.server_error'));
        }   
    }
}
