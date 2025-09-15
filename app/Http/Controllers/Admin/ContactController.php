<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Contacts;
use App\RouteHelper;
use App\Models\TokenHelper;
use App\Models\Responses;
use App\Models\User;
use ReallySimpleJWT\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Session;
use Validator;
use Mail;
use URL;
use Cookie;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
	private static $Contacts;
    private static $TokenHelper;
	private static $User;
	
	public function __construct(){
		self::$Contacts = new Contacts();
        self::$TokenHelper = new TokenHelper();
		self::$User = new User();
	}

    #admin dashboard page
    public function getList(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),11,'view')){return redirect('/admin/dashboard/');}
        return view('/admin/contacts/index');
    }
    public function listPaginate(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		
		$userDataNew = self::$User->select('type','id')->where(array('id' => $request->session()->get('admin_id')))->first();
		
        $query = self::$Contacts->where('read_status', '!=', 3);
		if($request->input('name')  && $request->input('name') != ""){
            $query->where('name', 'like', '%'.$request->input('name').'%');
		}
		if($request->input('email')  && $request->input('email') != ""){
            $query->where('email', 'like', '%'.$request->input('email').'%');
		}
		if($request->input('contact')  && $request->input('contact') != ""){
            $query->where('contact', 'like', '%'.$request->input('contact').'%');
		}
		if($userDataNew->type == 'Agent'){
			$query->where('user_id', $userDataNew->id);
		}
		$records =  $query->orderBy('id', 'ASC')->latest()->paginate(100);
		foreach($records as $key => $record){
			$userName = '--';
			if($record->user_id > 0){
				$userData = self::$User->select('company_name')->where('id',$record->user_id)->first();
				$userName = $userData->company_name;
			}
			$record->company_name = $userName;
		}
		
        return view('/admin/contacts/paginate',compact('records'));
    }


}
