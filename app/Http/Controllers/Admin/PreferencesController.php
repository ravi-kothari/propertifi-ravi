<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Zipcodes;
use App\Models\User;
use App\Models\UserPreferences;
use App\Models\UserZipcodes;
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

class PreferencesController extends Controller
{
	private static $Zipcodes;
	private static $UserZipcodes;
    private static $TokenHelper;
	private static $UserPreferences;
	private static $User;
	
	public function __construct(){
		self::$Zipcodes = new Zipcodes();
        self::$TokenHelper = new TokenHelper();
		self::$UserPreferences = new UserPreferences();
		self::$UserZipcodes = new UserZipcodes();
		self::$User = new User();
	}

    #admin dashboard page
    public function getList(Request $request,$id){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		
		if($id==''){return redirect('/admin/');}
		$agentID = $request->session()->get('admin_id');
		if($id > 0 && ($request->session()->get('admin_type') == 'Admin' || $request->session()->get('admin_type') == 'AccountManager')){
			$agentID = $id;
		}
		$agentData = self::$User->where(array('id' => $agentID,'type' => 'Agent'))->first();
		if(!isset($agentData->id)){
			return redirect('/admin/');
		}
		
        return view('/admin/preferences/index',['agentID' => $agentID]);
    }
	public function listPaginate(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		$counties = self::$UserZipcodes->select('county')->where('user_id',$request->agentID)->groupBy('county')->orderBy('county')->get();
		
		$zipcodes = [];
		foreach($counties as $key => $county){
			$zipcodes = self::$UserZipcodes->where('county',$county->county)->where('user_id',$request->agentID)->get();
			$county->zipcodes = $zipcodes;
		}
		
		$records = [];
		if($request->input('city_name')  && $request->input('city_name') != ""){
			$query = self::$Zipcodes->where('status', '!=', 3);
            $query->where('city', $request->input('city_name'));
			$records =  $query->orderBy('id', 'ASC')->get();
		}
		
		$agentID =$request->agentID; 
        return view('/admin/preferences/paginate',compact('records','zipcodes','counties','agentID'));
    }
	public function getSingleCountyData(Request $request){
		$county = str_replace("_"," ",$request->county);
		$county = ucfirst($county);
		$itemID = $request->county;
		$zipcodes = self::$UserZipcodes->where('county',$county)->where('user_id',$request->agentID)->get();
		return view('/admin/preferences/single_county',compact('zipcodes','itemID'));
			
	}
	public function removeAllZipcode(Request $request){
		$zipcodes = self::$Zipcodes->where('status', '!=', 3)->where('city', $request->input('cityName'))->orderBy('id', 'ASC')->get();
		foreach($zipcodes as $key => $zipcode):
			self::$UserZipcodes->where('county',$request->input('cityName'))->where('user_id',$request->agentID)->delete();
		endforeach;
		echo 'Success'; die;
	}
	public function setAllZipcode(Request $request){
		$zipcodes = self::$Zipcodes->where('status', '!=', 3)->where('city', $request->input('cityName'))->orderBy('id', 'ASC')->get();
		foreach($zipcodes as $key => $zipcode):
			$County = DB::table('zipcodes')->select('city')->where('zip_code',$zipcode->zip_code)->first();
			$getCount = self::$UserZipcodes->where('user_id',$request->agentID)->where('zipcode', $zipcode->zip_code)->first();
			if(!isset($getCount->id)){
				self::$UserZipcodes->create(['county' => $County->city,'zipcode' => $zipcode->zip_code, 'user_id' => $request->agentID]);
			}else{
				self::$UserZipcodes->where('id',$getCount->id)->update(['status' => 1,'county' => $County->city]);
			}
		endforeach;
		echo 'Success'; die;
	}
	public function setUserZipcode(Request $request){
		$County = DB::table('zipcodes')->select('city')->where('zip_code',$request->zipcode)->first();
		$getCount = self::$UserZipcodes->where('user_id',$request->agentID)->where('zipcode', $request->zipcode)->first();
		if(!isset($getCount->id)){
			self::$UserZipcodes->create(['county' => $County->city,'zipcode' => $request->zipcode, 'user_id' => $request->agentID]);
		}else{
			if($getCount->status == 1){
				self::$UserZipcodes->where('id',$getCount->id)->update(['status' => 2,'county' => $County->city]);
			}else{
				self::$UserZipcodes->where('id',$getCount->id)->update(['status' => 1,'county' => $County->city]);
			}
		}
		echo 'Success'; die;
	}
	public function searchZipcode(Request $request){
		$keyword = strtolower($_REQUEST["term"]);
		if (!$keyword) return;
		$records = DB::table('zipcodes')->select('city')->where('status',1)->where('city', 'like', $keyword.'%')->groupBy('city')->take(10)->get();
		$countRecord = $records->count();
		$html = '[';
		foreach($records as $key => $record):
			$html.= '{"label":"' . $record->city . '","value":"' . $record->city . '","name":"' . $record->city . '"}';
			if (($countRecord - 1) != $key){
				$html.= ',';
			}
		endforeach;
		$html.= ']';
		echo $html;
		die;
	}

}
