<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
	use HasFactory;
    protected $table = "users";
	const CREATED_AT = 'created_on';
	const UPDATED_AT = 'modified_on';
    protected $fillable = ['first_name','last_name','email','dob','salary','department_id'];

    const rowlimit = 10;

    /**
     * Method to check Userid exists.
     * @author Prasad
     */
    static function checkUserId($id)
    {
        return self::where('id',$id)->exists();
    }
    
    /**
     * Method to create new user.
     * @author Prasad
     */
    static function createRecord($request)
    {
        self::create([
			'first_name' => $request->first_name,
			'last_name' => $request->last_name,
			'email' => $request->email,
		    'dob' => $request->dob,
			'salary'=> $request->salary,
		    'department_id' => $request->department_id
	    ]);
    }

    /**
     * Method to update user record
     * @author Prasad
     */
    static function updateUserRecord($request)
    {
        self::where("id",$request->id)
            ->update([
                'first_name' => $request->first_name,
			    'last_name' => $request->last_name,
			    'email' => $request->email,
		        'dob' => $request->dob,
			    'salary'=> $request->salary,
		        'department_id' => $request->department_id
            ]);
    }

    /**
     * Method for date validation
     * Author Prasad
     */
    static function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Method to display user records
     * @author Prasad
     */
    static function userRecords($request)
    {
        $limit = $request->input('limit', self::rowlimit);

        $details = self::join("departments","departments.id","=","users.department_id");
        if($request->searchtext) {
           $details = $details->where('first_name',"like",'%'.$request->searchtext.'%');
           $details = $details->orwhere('last_name',"like",'%'.$request->searchtext.'%');
           $details = $details->orwhere('email',"like",'%'.$request->searchtext.'%');
        }
        if(is_numeric($request->searchtext)) $details = $details->orwhere('salary','=',$request->searchtext);
        if($request->orderby) {
            $order = json_decode($request->orderby,true);
            $details = $details->OrderBy($order['colname'], $order['sort']);
        } else {
            $details = $details->OrderByDesc('modified_on');
        }
        if(self::validateDate($request->searchtext))  $details = $details->orwhere('dob','=',$request->searchtext);
        $details = $details->paginate($limit,['users.id','users.first_name','users.last_name','users.email','users.dob','users.salary','departments.name']);
        return $details;
    }

    /**
     * Method to delete user
     * @author  Prasad
     */
    static function deleteUser($request)
    {
        $user = self::find($request->id);
        $user->delete();
    }

    /**
     * Method to display particular user record by id
     * @author  Prasad
     */
    static function viewUser($id)
    {
        $user = self::find($id);
        return $user;
    }
}
