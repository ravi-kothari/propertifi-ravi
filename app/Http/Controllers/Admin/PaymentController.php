<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Pricings;
use App\Models\Settings;
use App\RouteHelper;
use App\Models\TokenHelper;
use App\Models\Responses;
use App\Models\Credits;
use App\Models\State;
use App\Models\Cities;
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

class PaymentController extends Controller
{
	private static $Pricings;
    private static $TokenHelper;
	private static $Credits;
	private static $Settings;
	private static $State;
	private static $Cities;
	public function __construct(){
		self::$Pricings = new Pricings();
        self::$TokenHelper = new TokenHelper();
		self::$Credits = new Credits();
		self::$Settings = new Settings();
		self::$State = new State();
		self::$Cities = new Cities();
	}

    #admin dashboard page
    public function getList(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),4,'view')){return redirect('/admin/dashboard/');}
		$settings = self::$Settings->where('id', 1)->first();
		$states = self::$State->where(array('status' => 1,'country_id' => 231))->orderBy('state','ASC')->get();
        return view('/admin/payments/index',['settings' => $settings,'states' => $states]);
    }
    public function listPaginate(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        $query = self::$Credits->where('status', '!=', 3);
		
		$query->where('type', 'Credit');
		
		if($request->session()->get('admin_type') == 'Agent'){
			$query->where('user_id', $request->session()->get('admin_id'));
		}
		
		if($request->input('txn_id')  && $request->input('txn_id') != ""){
            $query->where('txn_id', 'like', '%'.$request->input('txn_id').'%');
		}
		if($request->input('state')  && $request->input('state') != ""){
            $query->where('state', $request->input('state'));
		}
		if($request->input('city')  && $request->input('city') != ""){
            $query->where('city', $request->input('city'));
		}
		
		$startDate1 = $endDate1 = '';
		if($request->input('s_date')  && $request->input('s_date') != ""){
			$startDate1 = date('Y-m-d',strtotime($request->input('s_date'))).' 00:00:00';
		}
		if($request->input('e_date')  && $request->input('e_date') != ""){
			$endDate1 = date('Y-m-d',strtotime($request->input('e_date'))).' 23:59:00';
		}
		
		if($startDate1 != "" && $endDate1 != ""){
			$query->where('created_at','>=',$startDate1)->where('created_at','<=',$endDate1);
		}
		
		$records =  $query->orderBy('id', 'DESC')->latest()->paginate(100);
        return view('/admin/payments/paginate',compact('records'));
    }
	public function exportPayment(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        $query = self::$Credits->where('status', '!=', 3);
		
		$query->where('type', 'Credit');
		
		if($request->session()->get('admin_type') == 'Agent'){
			$query->where('user_id', $request->session()->get('admin_id'));
		}
		
		if($request->input('txn_id')  && $request->input('txn_id') != ""){
            $query->where('txn_id', 'like', '%'.$request->input('txn_id').'%');
		}
		if($request->input('state')  && $request->input('state') != ""){
            $query->where('state', $request->input('state'));
		}
		if($request->input('city')  && $request->input('city') != ""){
            $query->where('city', $request->input('city'));
		}
		
		$startDate1 = $endDate1 = '';
		if($request->input('s_date')  && $request->input('s_date') != ""){
			$startDate1 = date('Y-m-d',strtotime($request->input('s_date'))).' 00:00:00';
		}
		if($request->input('e_date')  && $request->input('e_date') != ""){
			$endDate1 = date('Y-m-d',strtotime($request->input('e_date'))).' 23:59:00';
		}
		
		if($startDate1 != "" && $endDate1 != ""){
			$query->where('created_at','>=',$startDate1)->where('created_at','<=',$endDate1);
		}
		
		$records =  $query->orderBy('id', 'DESC')->get();
		$delimiter = ",";
		$filename = "payments_" . date('d_F_Y') . ".csv";

		$destination = "storage/csv/".$filename;
		//create a file pointer
		$f = fopen($destination,"w");
		
		$fields = array(

						'S.No', 
						
						'Invoice ID', 

						'Transaction ID',

						'Company Name',

						'State',

						'City',

						'Credits Purchased',

						'Total Amount',

						'Purchase Date',

						'Status'

						);

		fputcsv($f, $fields, $delimiter);
		
		foreach($records as $key => $record){
			
			$userData = DB::table('users')->where(array('id' => $record->user_id))->first();
			
			$state = 'N/A';
			if($record->state > 0){
				$sData = DB::table('states')->where(array('id' => $record->state))->first();
				if(isset($sData->id)){
					$state = $sData->state;
				}
			}
			
			$city = 'N/A';
			if($record->city > 0){
				$cData = DB::table('cities')->where(array('id' => $record->city))->first();
				if(isset($cData->id)){
					$city = $cData->city;
				}
			}
			
			$lineData = array(

						$key+1,

						$record->invoice_id,

						$record->txn_id,

						$userData->company_name,

						$state,

						$city,

						$record->credit,

						$record->amount,

						$record->created_at,

						'Success'                           

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
