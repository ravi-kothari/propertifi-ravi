<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Pricings;
use App\Models\Leads;
use App\Models\UserLeads;
use App\Models\User;
use App\RouteHelper;
use App\Models\TokenHelper;
use App\Models\Responses;
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

class LeadsController extends Controller
{
	private static $Pricings;
    private static $TokenHelper;
	private static $Leads;
	private static $UserLeads;
	private static $User;
	public function __construct(){
		self::$Pricings = new Pricings();
        self::$TokenHelper = new TokenHelper();
		self::$Leads = new Leads();
		self::$UserLeads = new UserLeads();
		self::$User = new User();
	}

    #admin dashboard page
    public function getList(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),2,'view')){return redirect('/admin/dashboard/');}
        return view('/admin/leads/index');
    }
    public function listPaginate(Request $request){
		
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		
		$userData = self::$User->where(array('id' => $request->session()->get('admin_id')))->first();
		
		if($userData->type == 'Admin' || $userData->type == 'AccountManager'){
		
			$query = self::$Leads->where('status', '!=', 3);
			if($request->input('category')  && $request->input('category') != ""){
				$query->where('category', $request->input('category'));
			}
			if($request->input('unique_id')  && $request->input('unique_id') != ""){
				$SearchKeyword = $request->input('unique_id');
				if(!empty($SearchKeyword)) {
                    $query->where('unique_id', 'like', '%'.$SearchKeyword.'%') 
                    ->orWhere('name', 'like', '%'.$SearchKeyword.'%')
                    ->orWhere('email', 'like', '%'.$SearchKeyword.'%')
					->orWhere('city', 'like', '%'.$SearchKeyword.'%')
					->orWhere('zipcode', 'like', '%'.$SearchKeyword.'%')
                    ->orWhere('phone', 'like', '%'.$SearchKeyword.'%');
                }
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
			foreach($records as $key => $record){
				$record->user_type = $userData->type;
			}
		
		}
		
		if($userData->type == 'Agent'){
		
			$query = self::$UserLeads->where('status', '!=', 3)->where('user_id', $userData->id);
			
			if($request->input('category')  && $request->input('category') != ""){
				$query->where('property_type', $request->input('category'));
			}
			if($request->input('unique_id')  && $request->input('unique_id') != ""){
				$SearchKeyword = $request->input('unique_id');
				if(!empty($SearchKeyword)) {
                    $query->where('unique_id', 'like', '%'.$SearchKeyword.'%') 
                    ->orWhere('name', 'like', '%'.$SearchKeyword.'%')
                    ->orWhere('email', 'like', '%'.$SearchKeyword.'%')
					->orWhere('city', 'like', '%'.$SearchKeyword.'%')
					->orWhere('zipcode', 'like', '%'.$SearchKeyword.'%')
                    ->orWhere('phone', 'like', '%'.$SearchKeyword.'%');
                }
			}
			$startDate1 = $endDate1 = '';
			if($request->input('s_date')  && $request->input('s_date') != ""){
				$startDate1 = date('Y-m-d',strtotime($request->input('s_date'))).' 00:00:00';
			}
			if($request->input('e_date')  && $request->input('e_date') != ""){
				$endDate1 = date('Y-m-d',strtotime($request->input('e_date'))).' 23:59:00';
			}
			
			if($startDate1 != "" && $endDate1 != ""){
				$query->where('lead_date','>=',$startDate1)->where('lead_date','<=',$endDate1);
			}
			$records =  $query->orderBy('id', 'DESC')->latest()->paginate(100);
			
			foreach($records as $key => $record){
				$leadData = self::$Leads->where('id',$record->lead_id)->first();
				$record->name = $leadData->name;
				$record->email = $leadData->email;
				$record->unique_id = $leadData->unique_id;
				$record->address = $leadData->address;
				$record->phone = $leadData->phone;
				$record->category = $leadData->category;
				$record->questions = $leadData->questions;
				$record->user_type = $userData->type;
				$record->city = $leadData->city;
				$record->zipcode = $leadData->zipcode;
				
			}
		
		}
		
        return view('/admin/leads/paginate',compact('records'));
    }
	public function exportLeads(Request $request){
		
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		
		$userData = self::$User->where(array('id' => $request->session()->get('admin_id')))->first();
		
		if($userData->type == 'Admin' || $userData->type == 'AccountManager'){
		
			$query = self::$Leads->where('status', '!=', 3);
			if($request->input('category')  && $request->input('category') != ""){
				$query->where('category', $request->input('category'));
			}
			if($request->input('unique_id')  && $request->input('unique_id') != ""){
				$SearchKeyword = $request->input('unique_id');
				if(!empty($SearchKeyword)) {
                    $query->where('unique_id', 'like', '%'.$SearchKeyword.'%') 
                    ->orWhere('name', 'like', '%'.$SearchKeyword.'%')
                    ->orWhere('email', 'like', '%'.$SearchKeyword.'%')
					->orWhere('city', 'like', '%'.$SearchKeyword.'%')
					->orWhere('zipcode', 'like', '%'.$SearchKeyword.'%')
                    ->orWhere('phone', 'like', '%'.$SearchKeyword.'%');
                }
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
		
		}
		
		if($userData->type == 'Agent'){
		
			$query = self::$UserLeads->where('status', '!=', 3)->where('user_id', $userData->id);
			
			if($request->input('category')  && $request->input('category') != ""){
				$query->where('property_type', $request->input('category'));
			}
			if($request->input('unique_id')  && $request->input('unique_id') != ""){
				$SearchKeyword = $request->input('unique_id');
				if(!empty($SearchKeyword)) {
                    $query->where('unique_id', 'like', '%'.$SearchKeyword.'%') 
                    ->orWhere('name', 'like', '%'.$SearchKeyword.'%')
                    ->orWhere('email', 'like', '%'.$SearchKeyword.'%')
					->orWhere('city', 'like', '%'.$SearchKeyword.'%')
					->orWhere('zipcode', 'like', '%'.$SearchKeyword.'%')
                    ->orWhere('phone', 'like', '%'.$SearchKeyword.'%');
                }
			}
			$startDate1 = $endDate1 = '';
			if($request->input('s_date')  && $request->input('s_date') != ""){
				$startDate1 = date('Y-m-d',strtotime($request->input('s_date'))).' 00:00:00';
			}
			if($request->input('e_date')  && $request->input('e_date') != ""){
				$endDate1 = date('Y-m-d',strtotime($request->input('e_date'))).' 23:59:00';
			}
			
			if($startDate1 != "" && $endDate1 != ""){
				$query->where('lead_date','>=',$startDate1)->where('lead_date','<=',$endDate1);
			}
			$records =  $query->orderBy('id', 'DESC')->get();
		}
		
		$delimiter = ",";
		$filename = "leads_" . date('d_F_Y') . ".csv";
		
		$destination = "storage/csv/".$filename;
		//create a file pointer
		$f = fopen($destination,"w");
		
		//set column headers
		$fields = array(
						'S.No', 
						'Customer Name',
						'Customer Email',
						'Lead ID',
						'Property Address',
						'Contact No',
						'Property Type',
						'Date'
						);
		fputcsv($f, $fields, $delimiter);
		foreach($records as $key => $record):
			
			$category = 'N/A';
			if($record->category == 1){
			$category = 'Single Family';
			}else if($record->category == 2){
			$category = 'Multi Family';
			}else if($record->category == 3){
			$category = 'Association Property';
			}else if($record->category == 8){
			$category = 'Commercial Property';
			}
		
			if($userData->type == 'Admin' || $userData->type == 'AccountManager'){
				$lineData = array(
							$key+1,
							$record->name,
							$record->email,
							$record->unique_id,
							$record->address,
							$record->phone,
							$category,
							$record->created_at                          
						);
			}
			if($userData->type == 'Agent'){
			$leadData = self::$Leads->where('id',$record->lead_id)->first();
			$lineData = array(
							$key+1,
							$leadData->name,
							$leadData->email,
							$leadData->unique_id,
							$leadData->address,
							$leadData->phone,
							$category,
							$record->created_at                          
						);
			}
			fputcsv($f, $lineData, $delimiter);
		endforeach;
		$lineData2 = array('','');						
		fputcsv($f, $lineData2, $delimiter);                     
		
		fclose ($f);

		echo env('APP_URL').$destination;		
		exit;
		
    }
	public function viewPage(Request $request,$row_id){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		$RowID =  base64_decode($row_id);
		$rowData = self::$Leads->where(array('id' => $RowID))->first();
        if(isset($rowData->id)){
			
			$users = self::$UserLeads->where('status', '!=', 3)->where('lead_id',$rowData->id)->get();
			foreach($users as $key => $user){
				$userData = self::$User->where(array('id' => $user->user_id))->first();
				$user->company_name = $userData->company_name;
				$user->email = $userData->email;
				$user->address = $userData->address;
				$user->mobile = $userData->mobile;
			}
            return view('/admin/leads/view_lead_history',compact('rowData','row_id','users'));
        }else{
            return redirect('/admin/leads');
        }
    }
	public function leadQuestionAnswer(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		$rowData = self::$Leads->where(array('id' => $request->leadID))->first();
		$questions = json_decode($rowData->questions);
		
		$results = [];
		foreach($questions as $key => $question){
			$results[$key]['question'] = $question->question;
			$results[$key]['answer'] = is_array($question->answer) ? implode(', ',$question->answer) : $question->answer ;
		}
		
		return view('/admin/leads/question_answers',compact('results'));
    }
	public function sendLead(Request $request){
		$leads = DB::table('leads')->get();
		$currentTime = time();
		foreach($leads as $key => $lead){
			
			$users = DB::table('user_preferences')->join('user_zipcodes', 'user_preferences.user_id', '=', 'user_zipcodes.user_id')->select('user_preferences.*')->where('user_preferences.pricing_id',$lead->category)->where('user_preferences.status',1)->where('user_zipcodes.zipcode',$lead->zipcode)->where('user_zipcodes.status',1)->get();
			foreach($users as $key => $user){
				$sendTime = strtotime(date('Y-m-d H:i:s',strtotime('+'.$user->timings.' minutes', strtotime($lead->created_at))));
				if($currentTime >= $sendTime){
					$leadLinked = DB::table('user_leads')->where('user_id',$user->user_id)->where('lead_id',$lead->id)->where('status',1)->first();
					if(!isset($leadLinked->id)){
						$tierData = DB::table('tiers')->where('id',$user->tier_id)->where('status',1)->first();
						if(isset($tierData->id)){
							if($tierData->price > 0){
								$userData = DB::table('users')->select('credits')->where('id',$user->user_id)->first();
								if($userData->credits >= $tierData->price){
									DB::table('user_leads')->insert(['user_id' => $user->user_id,'lead_id' => $lead->id,'status' => 1,'lead_unique_id' => $lead->unique_id,'property_type' => $lead->category,'lead_date' => $lead->created_at,'price' => $tierData->price,'tier_id' => $tierData->id]);
									
									$remainingBalance = $userData->credits-$tierData->price;
									DB::table('users')->where('id',$user->user_id)->update(['credits' => $remainingBalance]);
								}
							}else{
								DB::table('user_leads')->insert(['user_id' => $user->user_id,'lead_id' => $lead->id,'status' => 1,'lead_unique_id' => $lead->unique_id,'property_type' => $lead->category,'lead_date' => $lead->created_at,'price' => $tierData->price,'tier_id' => $tierData->id]);
							}
						}
					}
				}
			}
		}
		echo '<pre>';
		print_r($leads); die;
		die;
	}


}
