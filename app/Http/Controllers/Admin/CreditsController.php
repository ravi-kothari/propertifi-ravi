<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Credits;
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
use App\Models\User;

class CreditsController extends Controller
{
	private static $Credits;
    private static $TokenHelper;
	private static $Settings;
	private static $User;
	public function __construct(){
		self::$Credits = new Credits();
        self::$TokenHelper = new TokenHelper();
		self::$Settings = new Settings();
		self::$User = new User();
	}

	#add new
    public function addCredit(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if($request->input()){
			$validator = Validator::make($request->all(), [
                'credit' => 'required|numeric',
				'agent_id' => 'required|numeric',
            ],[
                'credit.required' => 'Please enter credit.',
				'agent_id.required' => 'Please enter agent_id.',
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('credit')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('credit')));die;
				}
				if($errors->first('agent_id')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('agent_id')));die;
				}
			}else{
				
				$userData = self::$User->where('id',$request->agent_id)->first();
				$lastOrder = self::$Credits->select('in_no','id')->orderBy('id','DESC')->first();
				$inc_no = 1;
				if(isset($lastOrder->id)){
					$inc_no = $lastOrder->in_no+1;
				}
				
				$inc_no = str_pad($inc_no, 3, "0", STR_PAD_LEFT);
				$setData['invoice_id'] = date('Y').'/'.date('m').'/P'.$inc_no;
				$setData['in_no'] = $inc_no;
				
				$settings = self::$Settings->where('id', 1)->first();
				$totalAmt = $settings->per_credit_amt*$request->input('credit');
				
                $setData['user_id'] = $request->input('agent_id');
				$setData['type'] = 'Credit';
				$setData['credit'] = $request->input('credit');
				$setData['state'] = $userData->state;
				$setData['city'] = $userData->city;
				$setData['amount'] = $totalAmt;
				$setData['txn_id'] = rand(10001,99999);
				$setData['added_by'] = $request->session()->get('admin_id');
				$setData['status'] = 2;
				
				$record = self::$Credits->CreateRecord($setData);
                echo json_encode(array('heading'=>'Success','msg'=>'Credit added successfully','o_id'=>$record->id));die;

			}
		}
		return view('/admin/agents/add-page');
    }
	public function getTotalCredits(Request $request){
		$record = self::$Credits->where('type','Credit')->where('status',1)->where('user_id',$request->id)->sum('credit');
		echo $record; die;
	}


}
