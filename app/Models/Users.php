<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
	use HasFactory;

    protected $table = "users";
	const CREATED_AT = 'created_on';
	const UPDATED_AT = 'modified_on';

    

    protected $fillable = ['first_name','last_name','email','dob','salary','department_id'];


    function CreateRecord($request)
    {
        $user = self::create([
			'first_name' => $request->first_name,
			'last_name' => $request->last_name,
			'email' => $request->email,
		    'dob' => $request->dob,
			'salary'=> $request->salary,
		    'department_id' => $request->department_id
	    ]);
        return $user;
    }

    function UpdateUserRecord($request)
    {
        $user = self::find($request->id);
        if($user){
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
		    $user->email = $request->email;
	        $user->dob = $request->dob;
    	    $user->salary = $request->salary;
		    $user->department_id = $request->department_id;
            $user->save();
        return $user;
        }
    }

    function UserRecords($request){
        $limit = $request->input('limit',10);

        $details = self::join("departments","departments.id","=","users.department_id");
        if($request->searchtext){
           $details = $details->where('first_name',"like",'%'.$request->searchtext.'%');
           $details = $details->orwhere('last_name',"like",'%'.$request->searchtext.'%');
           $details = $details->orwhere('email',"like",'%'.$request->searchtext.'%');
           $details = $details->orwhere('dob','=',$request->searchtext);
         }
         if(is_numeric($request->searchtext)) $details = $details->orwhere('salary','=',$request->searchtext);

         //Order by desc
         $orderby = $request->input('orderby', 'modified_on');
         $details = $details->OrderByDesc($orderby);
         $details = $details->paginate($limit,['users.id','users.first_name','users.last_name','users.email','users.dob','users.salary','departments.name']);
         return $details;
        }

        function DeleteUSer($request){
            $user = self::find($request->id);
            if($user){
                $user->delete();
                return $user;
            }
        }

        function ViewUser($id){
            $user = self::find($id);
            if($user){
                return $user;
            }
        }
   }
