<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Contacts;
use App\RouteHelper;
use App\Models\TokenHelper;
use App\Models\Responses;
use App\Models\User;
use App\Models\Newsletters;
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

class NewsletterController extends Controller
{
	private static $Contacts;
    private static $TokenHelper;
	private static $User;
	private static $Newsletters;
	
	public function __construct(){
		self::$Contacts = new Contacts();
        self::$TokenHelper = new TokenHelper();
		self::$User = new User();
		self::$Newsletters = new Newsletters();
	}

    #admin dashboard page
    public function getList(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),12,'view')){return redirect('/admin/dashboard/');}
        return view('/admin/newsletters/index');
    }
    public function listPaginate(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        $query = self::$Newsletters->where('status', '!=', 3);
		if($request->input('email')  && $request->input('email') != ""){
            $query->where('email', 'like', '%'.$request->input('email').'%');
		}
		$records =  $query->orderBy('id', 'ASC')->latest()->paginate(100);
		
        return view('/admin/newsletters/paginate',compact('records'));
    }
	public function exportData(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        $query = self::$Newsletters->where('status', '!=', 3);
		
		if($request->input('email')  && $request->input('email') != ""){
            $query->where('email', 'like', '%'.$request->input('email').'%');
		}
		
		$records =  $query->orderBy('id', 'DESC')->get();
		
		$delimiter = ",";
		$filename = "newsletters_" . date('d_F_Y') . ".csv";

		$destination = "storage/csv/".$filename;
		//create a file pointer
		$f = fopen($destination,"w");
		
		$fields = array(

						'S.No', 
						
						'Email Address'

						);

		fputcsv($f, $fields, $delimiter);
		
		foreach($records as $key => $record){
			
			
			$lineData = array(

						$key+1,

						$record->email                        

					);

			fputcsv($f, $lineData, $delimiter);
		}
		
		
		$lineData2 = array('','');						
		fputcsv($f, $lineData2, $delimiter);                     
		fclose ($f);
		echo env('APP_URL').$destination;		
		exit;
		
    
    }


}
