<?php
namespace App\Helpers;
use DB;
use Session;
class Helper{
	public static function checkPermission($userID,$mid,$colum){
		$userData = DB::table('users')->select('role_id','type')->where(array('id' => $userID))->first();
		if($userData->type == 'AccountManager'){
			$roleData = DB::table('roles')->where(array('id' => $userData->role_id))->whereRaw("FIND_IN_SET('".$mid."', $colum)")->first();
			if(isset($roleData->id)){
				return true;
			}
			return false;
		}
       	return true;
	}
	public static function GetPricingSet($id,$pid){
		$records = DB::table('user_preferences')->where(array('user_id' => $id,'pricing_id' => $pid,'status' => 1))->count();
        return $records;
	}
	public static function GetTierInfo($id,$pid){
		$records = DB::table('user_preferences')->where(array('user_id' => $id,'pricing_id' => $pid,'status' => 1))->first();
        return $records;
	}
	
	public static function GetPricingChild($pid){
		$records = DB::table('pricings')->where(array('parent_id' => $pid))->get();
        return $records;
	}
	public static function GetTiers(){
		$records = DB::table('tiers')->where(array('status' => 1))->orderBy('id','ASC')->get();
        return $records;
	}
	public static function GetUserData($pid){
		$records = DB::table('users')->where(array('id' => $pid))->first();
        return $records;
	}
	public static function getProfileCompleted($uid){
		$records = DB::table('users')->where(array('id' => $uid))->first();
       	$count = 0;
		if(!empty($records->company_name)){
			$count++;
		}
		if(!empty($records->email)){
			$count++;
		}
		if(!empty($records->mobile)){
			$count++;
		}
		if(!empty($records->address)){
			$count++;
		}
		if(!empty($records->state)){
			$count++;
		}
		if(!empty($records->city)){
			$count++;
		}
		if(!empty($records->photo)){
			$count++;
		}
		if(!empty($records->about)){
			$count++;
		}
		if(!empty($records->p_contact_name)){
			$count++;
		}
		if(!empty($records->p_contact_no)){
			$count++;
		}
		if(!empty($records->p_contact_email)){
			$count++;
		}
		if(!empty($records->single_family)){
			$count++;
		}
		if(!empty($records->multi_family)){
			$count++;
		}
		if(!empty($records->association_property)){
			$count++;
		}
		if(!empty($records->commercial_property)){
			$count++;
		}
		$percent = ($count/15)*100;
		return round($percent);
	}
	public static function getZipcodePreference($uid){
		$records = DB::table('user_zipcodes')->where(array('user_id' => $uid,'status' => 1))->count();
        return $records;
	}
	public static function getZipcodeExist($uid,$county){
		$records = DB::table('user_zipcodes')->where(array('county' => $county, 'user_id' => $uid,'status' => 1))->count();
        return $records;
	}
	public static function getPropertTypePreference($uid){
		$records = DB::table('user_preferences')->where(array('user_id' => $uid,'status' => 1))->count();
        return $records;
	}
	public static function GetStateData($pid){
		if($pid > 0){
			$records = DB::table('states')->where(array('id' => $pid))->first();
			if(isset($records->id)){
				return $records->state;
			}else{
				return 'N/A';
			}
		}else{
			return 'N/A';
		}
	}
	public static function GetCityData($pid){
		if($pid > 0){
			$records = DB::table('cities')->where(array('id' => $pid))->first();
			if(isset($records->id)){
				return $records->city;
			}else{
				return 'N/A';
			}
		}else{
			return 'N/A';
		}
	}
	public static function GetZipcodeSet($id,$zipcode){
		$records = DB::table('user_zipcodes')->where(array('user_id' => $id,'zipcode' => $zipcode,'status' => 1))->count();
        return $records;
	}

}
?>