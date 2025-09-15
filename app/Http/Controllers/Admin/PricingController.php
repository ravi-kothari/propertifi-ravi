<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Pricings;
use App\Models\User;
use App\Models\UserPreferences;
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
use App\Models\Settings;

class PricingController extends Controller
{
	private static $Pricings;
	private static $User;
    private static $TokenHelper;
	private static $UserPreferences;
	private static $Settings;
	
	public function __construct(){
		self::$Pricings = new Pricings();
        self::$TokenHelper = new TokenHelper();
		self::$UserPreferences = new UserPreferences();
		self::$User = new User();
		self::$Settings = new Settings();
	}
	#getCoverage
    public function getCoverage(Request $request,$id){
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
        return view('/admin/pricing/coverage',['agentID' => $agentID]);
    }
	public function listCoveragePaginate(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		$query = self::$Pricings->where('status', '!=', 3)->where('parent_id', 0);
		$records =  $query->orderBy('id', 'ASC')->latest()->paginate(100);
        return view('/admin/pricing/coverage_paginate',['records' => $records,'agentID' => $request->agentID]);
    }
	public function setCoverage(Request $request){
		$getData = self::$UserPreferences->where('user_id',$request->agentID)->where('pricing_id', $request->id)->first();
		if(isset($getData->id)){
			if($getData->status == 1){
				$tierData = DB::table('tiers')->where('id',1)->first();
				self::$UserPreferences->where('id', $getData->id)->update(['status' => 2,'tier_id' => 1,'timings' => $tierData->timings]);
			}else{
				self::$UserPreferences->where('id', $getData->id)->update(['status' => 1]);
			}
		}else{
			$tierData = DB::table('tiers')->where('id',1)->first();
			self::$UserPreferences->create(['pricing_id' => $request->id,'tier_id' => 1,'timings' => $tierData->timings,'user_id' => $request->agentID]);
		}
		echo 'Success'; die;
    }
	public function setTier(Request $request){
		$getData = self::$UserPreferences->where('user_id',$request->agentID)->where('pricing_id', $request->id)->first();
		$tierData = DB::table('tiers')->where('id',$request->tid)->first();
		if(isset($getData->id)){
			self::$UserPreferences->where('id', $getData->id)->update(['tier_id' => $request->tid,'timings' => $tierData->timings]);
		}else{
			self::$UserPreferences->create(['pricing_id' => $request->id,'tier_id' => $request->tid,'timings' => $tierData->timings,'user_id' => $request->agentID]);
		}
		echo 'Success'; die;
    }
    #admin dashboard page
    public function getList(Request $request){
		if(!$this->checkPermission($request->session()->get('admin_id'),5,'view')){return redirect('/admin/dashboard/');}
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		$setting  = self::$Settings->where(array('id' => 1))->first();
        return view('/admin/pricing/index',compact('setting'));
    }
    public function listPaginate(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        $query = self::$Pricings->where('status', '!=', 3);
		if($request->input('title')  && $request->input('title') != ""){
            $SearchKeyword = $request->input('title');
            $query->where('property_type', 'like', '%'.$SearchKeyword.'%');
		}
		$records =  $query->orderBy('id', 'ASC')->latest()->paginate(100);
        return view('/admin/pricing/paginate',compact('records'));
    }

    #pricingUpdate
    public function pricingUpdate(Request $request){
		
		$validator = Validator::make($request->all(), [
				'price' => 'required|numeric',
            ],[
                'price.required' => 'Please enter price.',
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('price')){
                    echo 'Error'; die;
				}
			}else{
				$price = 0;
				if($request->price > 0){
					$price = $request->price;
				}
				self::$Pricings->where('id',$request->id)->update(['bid_price' => $price]);
				echo 'Success'; die;
			}
	}
	#categoryUpdate
    public function categoryUpdate(Request $request){
		
		$validator = Validator::make($request->all(), [
				'category' => 'required',
            ],[
                'category.required' => 'Please enter category.',
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('category')){
                    echo 'Error'; die;
				}
			}else{
				self::$Pricings->where('id',$request->id)->update(['property_type' => $request->category]);
				echo 'Success'; die;
			}
	}

}
