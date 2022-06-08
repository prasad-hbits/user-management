<?php

namespace App\Models;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        $details = $details->paginate($limit,['users.id','users.first_name','users.last_name','users.email','users.dob','users.salary',DB::raw("DATE_FORMAT(users.created_on,'%Y-%m-%d') as display_date"),'departments.name as department_name']);
        return $details;
    }

    /**
     * Method to delete user
     * @author  Prasad
     */
    static function deleteUser($request)
    {
        self::find($request->id)->delete();
    }

    /**
     * Method to display particular user record by id
     * @author  Prasad
     */
    static function viewUser($id)
    {
        $users = self::select('users.first_name','users.last_name','users.email','users.dob','users.salary','users.created_on','departments.name as department_name')->join('departments','users.department_id','=','departments.id')->where('users.id','=',$id)->first();
        $users->display_date = Carbon::createFromFormat('Y-m-d H:i:s', $users['created_on'])->format('Y-m-d');
        return $users;
    }
}
