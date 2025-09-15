<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
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

use App\Models\State;
use App\Models\User;
use App\Mail\AccountActiveMail;

class AjaxController extends Controller
{
    private static $TokenHelper;
	private static $User;
	public function __construct(){
        self::$TokenHelper = new TokenHelper();
		self::$User = new User();
	}
	
	public function changePrice(Request $request){
		if(!$request->session()->has('admin_email')){echo 'SessionExpire'; die;}
		$value = $request->input('value');
		$rowID = $request->input('id');
		$field = $request->input('field');
		if(DB::table('products')->where(array('id' => $rowID))->update(array($field => $value))){
			echo "Success"; die;
		}else{
			echo "Error"; die;
		}
	}
	
	public function changeProductStatus(Request $request){
		if(!$request->session()->has('admin_email')){echo 'SessionExpire'; die;}
		$tableName = $request->input('table');
		$rowID = $request->input('rowID');
		$new_status = $request->input('status');
		
		$record = DB::table($tableName)->select('status')->where(array('id' => $rowID))->first();
		$status = $record->status;
		if($tableName != "" && $rowID != "" && $status != "" && is_numeric($rowID) && is_numeric($status)){            
            $newStatus = $new_status;
			if($newStatus == 3){
				DB::table($tableName)->where(array('id' => $rowID))->update(array('status' => $newStatus, 'moderation_status' => 'Pending'));
			}else{
				DB::table($tableName)->where(array('id' => $rowID))->update(array('status' => $newStatus));
			}			
			echo 'Success';die;
		}else{
			echo 'InvalidData'; die;
		}
    }

    public function changeStatus(Request $request){
		if(!$request->session()->has('admin_email')){echo 'SessionExpire'; die;}
		$tableName = $request->input('table');
		$rowID = $request->input('rowID');
		$status = $request->input('status');
		if($tableName != "" && $rowID != "" && $status != "" && is_numeric($rowID) && is_numeric($status)){
			
			$newStatus = $status == 1 ? 2 : 1;
			if($tableName == 'users'){
				$userData = self::$User->where('id',$rowID)->first();
				if($userData->email_varification == 2 && $status == 2){
					$newStatus = 2;
				}
			}
			DB::table($tableName)->where(array('id' => $rowID))->update(array('status' => $newStatus));
			
			if($tableName == 'users' && $status == 2){
				$userData = self::$User->where('id',$rowID)->first();
				if($userData->temp_pass != ''){
					$data = [
					'userData' => $userData,
					'subject' => 'Your Propertifi Account is Active! Complete Your Profile to Get Leads',
					];
					Mail::to([strtolower(trim($userData->email))])->bcc([strtolower(env('BCC')),strtolower(env('BCC2')),strtolower(env('BCC3')),strtolower(env('BCC4')),strtolower(env('BCC5'))])->send(new AccountActiveMail($data));
					//self::$User->where('id',$rowID)->update(['temp_pass' => NULL]);
				}
			}
			
			echo 'Success';die;
		}else{
			echo 'InvalidData'; die;
		}
    }

    public function deleteRecord(Request $request){
		if(!$request->session()->has('admin_email')){echo 'SessionExpire'; die;}
		$tableName = $request->input('table');
		$rowID = $request->input('rowID');
		if($tableName != "" && $rowID != "" && is_numeric($rowID)){
            DB::table($tableName)->where(array('id' => $rowID))->update(array('status' => 3));
			echo 'Success';die;
		}else{
			echo 'InvalidData'; die;
		}
    }

    public function productsChangeStatus(Request $request){
		if(!$request->session()->has('admin_email')){echo 'SessionExpire'; die;}
		$productIDs = $request->input('productIDs');
		$status = $request->input('status');
        if(count($productIDs) == 0){
            echo 'Please select Products.';die;
        }
		if($status != "" && is_numeric($status)){
            foreach($productIDs as $rowID){
                $newStatus = $status == 1 ? 2 : 1;
                DB::table('products')->where(array('id' => $rowID))->update(array('status' => $newStatus));
            }
			echo 'Success';die;
		}else{
			echo 'InvalidData'; die;
		}
    }

    public function productsDeleteRecord(Request $request){
		if(!$request->session()->has('admin_email')){echo 'SessionExpire'; die;}
		$productIDs = $request->input('productIDs');
		$status = $request->input('status');
        if(count($productIDs) == 0){
            echo 'Please select Products.';die;
        }
        foreach($productIDs as $rowID){
            $newStatus = $status == 1 ? 2 : 1;
            //DB::table('products')->where('id', $rowID)->delete();
            DB::table('products')->where(array('id' => $rowID))->update(array('status' => 3));
        }
        echo 'Success';die;

    }

	public function getLicenseByLicenseType(Request $request){
		$licenses = self::$Licenses->where('status',1)->where('license_type_id',$request->input('license_type_id'))->get();
		return view('/admin/ajax/license',compact('licenses'));
	}

    public function getHospitalDepartments(Request $request){
		$HospitalDepartments = self::$HospitalDepartments->where('status',1)->where('hospital_id',$request->input('hospital_id'))->get();
		return view('/admin/ajax/hospital_departments',compact('HospitalDepartments'));
	}
	
	
	public function getState(Request $request){
		if($request->ajax()){
			$country_id = $request->input('countryId');
			$states = DB::table('states')->where('country_id',$country_id)->where('status',1)->orderBy('state')->pluck('state','id');

			echo view('/admin/ajax/get_state',compact('states'));
		}
		exit;
	}
	
	public function getCity(Request $request){
		if($request->ajax()){
			$state_id = $request->input('stateId');
			$cities = DB::table('cities')->where('state_id',$state_id)->where('status',1)->orderBy('city')->pluck('city','id');

			echo view('/admin/ajax/get_city',compact('cities'));
		}
		exit;
	}

}
