<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\RouteHelper;
use App\Models\TokenHelper;
use App\Models\Responses;
use App\Models\State;
use App\Models\UserLeads;
use App\Models\SendSms;
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
use Illuminate\Support\Str;
use App\Models\Lead;
use App\Models\Credits;
use App\Models\User;
use App\Mail\OrderMail;
use App\Mail\OtpMail;
use App\Mail\InvoiceMail;
use App\Mail\SignupMail;
use App\Mail\EmailvarificationMail;
use App\Mail\AccountActiveMail;
use App\Mail\ResetPasswordMail;

class AdminController extends Controller
{
	private static $UserModel;
    private static $TokenHelper;
	private static $Leads;
	private static $Credits;
	private static $User;
	private static $State;
	private static $SendSms;
	private static $UserLeads;
	public function __construct(){
		self::$UserModel = new AdminUser();
        self::$TokenHelper = new TokenHelper();
		self::$Leads = new Lead();
		self::$Credits = new Credits();
		self::$User = new User();
		self::$State = new State();
		self::$SendSms = new SendSms();
		self::$UserLeads = new UserLeads();
	}
	public function builtSlug($input_lines){
		preg_match_all("/[0-9A-Za-z\s]/", trim($input_lines), $output_array);
		$slug = strtolower(preg_replace("/[\s]/", "-", join($output_array[0])));
		$temp_string = preg_replace("/-{2,}/", "-", $slug);
		return $temp_string;
	}
	# admin dashboard page
    public function dashboard(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		
		$userData = self::$User->where(array('id' => $request->session()->get('admin_id')))->first();
		if($userData->type == 'Admin' || $userData->type == 'AccountManager'){
			$leads = self::$Leads->where('status',1)->orderBy('id', 'DESC')->get()->take(5);
			$leadCount = self::$Leads->where('status',1)->orderBy('id', 'DESC')->count();
			$totalCredit = self::$Credits->where('status',1)->sum('credit');
			$totalCreditAmt = self::$Credits->where('status',1)->sum('amount');
			$agents = self::$UserModel->where('type','Agent')->where('status','!=',3)->count();
		}else{
			$leads = self::$UserLeads->where('status',1)->where('user_id', $userData->id)->get()->take(5);
			foreach($leads as $key => $record){
				$leadData = self::$Leads->where('id',$record->lead_id)->first();
				if(isset($leadData->id)){
					$record->name = $leadData->name;
					$record->email = $leadData->email;
					$record->unique_id = $leadData->unique_id;
					$record->address = $leadData->address;
					$record->phone = $leadData->phone;
					$record->category = $leadData->category;
					$record->questions = $leadData->questions;
					$record->user_type = $userData->type;
				}
			}
			$leadCount = self::$UserLeads->where('status',1)->where('user_id', $userData->id)->count();
			$totalCredit = self::$Credits->where('status',1)->where('user_id', $userData->id)->sum('credit');
			$totalCreditAmt = self::$Credits->where('status',1)->where('user_id', $userData->id)->sum('amount');
			$agents = 1;
		}
		
		
        return view('/admin/dashboard',['leads' => $leads,'leadCount' => $leadCount,'totalCredit' => $totalCredit,'totalCreditAmt' => $totalCreditAmt,'agents' => $agents]);
    }

    # admin dashboard page
    public function logout(Request $request){
        $request->session()->flush();
        return redirect('/admin/');
   }
    # admin login page
    public function login(Request $request){
		
        if($request->session()->has('admin_email')){return redirect('/admin/dashboard/');}
        $cookieUsername = Cookie::get('cookieUsername');
        $cookiePassword = Cookie::get('cookiePassword');
        return view('/admin/login',compact('cookieUsername','cookiePassword'));
    }
	#forgotPassword
    public function forgotPassword(Request $request){
		
        if($request->session()->has('admin_email')){return redirect('/admin/dashboard/');}
		
		if($request->input()){
			$validator = Validator::make($request->all(), [
				'email' => 'required|email'
            ],[
				'email.required' => 'Please enter email.'
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('email')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('email')));die;
				}
			}else{
				$userData = self::$User->where('email',$request->email)->first();
				if(isset($userData->id)){
					$data = [
					'userData' => $userData,
					'subject' => 'Reset Your Password!',
					];
					Mail::to([strtolower(trim($request->email))])->send(new ResetPasswordMail($data));
				}else{
					return json_encode(array('heading'=>'Error','msg'=>'This email address is not registered.'));die;
				}
			}
		}
		
        return view('/admin/forgot_password');
    }
	#resetPassword
    public function resetPassword(Request $request,$time,$id){
		
        if($request->session()->has('admin_email')){return redirect('/admin/dashboard/');}
        return view('/admin/reset_password',compact('time','id'));
    }
	#updateNewPassword
    public function updateNewPassword(Request $request){
		
        if($request->session()->has('admin_email')){return redirect('/admin/dashboard/');}
		
		if($request->input()){
			$validator = Validator::make($request->all(), [
				'token' => 'required|numeric',
				'key' => 'required|numeric',
				'password' => 'required',
				'c_password' => 'required'
            ],[
				'token.required' => 'Please enter token.',
				'key.required' => 'Please enter key.',
				'password.required' => 'Please enter password.',
				'c_password.required' => 'Please enter confirm password.',
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('token')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('token')));die;
				}
				if($errors->first('key')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('key')));die;
				}
				if($errors->first('password')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('password')));die;
				}
				if($errors->first('c_password')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('c_password')));die;
				}
			}else{
				if($request->password == $request->c_password){
					if(strlen($request->password) > 6){
						$Password = password_hash($request->password,PASSWORD_BCRYPT);
                    	self::$User->where(array('id' => $request->token))->update(array('password' => $Password));
						return json_encode(array('heading'=>'Success','msg'=>'Password updated successfully.'));die;
					}else{
						return json_encode(array('heading'=>'Error','msg'=>'Password should be more then 6 characters.'));die;
					}
				}else{
					return json_encode(array('heading'=>'Error','msg'=>'Password do not match'));die;
				}
			}
		}
		
        return view('/admin/forgot_password');
    }
	# verifyEmail
    public function verifyEmail(Request $request,$id){
		$id = base64_decode($id);
		if($id > 0){
			$userData = self::$User->where('id',$id)->first();
			if(isset($userData->id)){
				self::$User->where('id',$id)->update(['email_varification' => 1]);
				return view('/admin/email_verification');
			}else{
				return redirect('/admin/');
			}
			
		}else{
			return redirect('/admin/');
		}
		
	}
	# createAgentAccount
    public function createAgentAccount(Request $request){
		
        if($request->session()->has('admin_email')){return redirect('/admin/dashboard/');}
        $cookieUsername = Cookie::get('cookieUsername');
        $cookiePassword = Cookie::get('cookiePassword');
		$states = self::$State->where(array('status' => 1,'country_id' => 231))->orderBy('state','ASC')->get();

        return view('/admin/create_agent_account',compact('cookieUsername','cookiePassword','states'));
    }
	# registerAgent
    public function registerAgent(Request $request){
		$validator = Validator::make($request->all(), [
                'company_name' => 'required',
				'email' => 'required|email',
				'contact' => 'required|numeric',
				'portfolio_type' => 'required',
				'units' => 'required|numeric',
				'address' => 'required',
				'state' => 'required',
				'city' => 'required',
				'p_contact_name' => 'required',
				'p_contact_no' => 'required|numeric',
				'p_contact_email' => 'required|email'
            ],[
                'company_name.required' => 'Please enter company name.',
				'email.required' => 'Please enter email.',
				'contact.required' => 'Please enter contact.',
				'portfolio_type.required' => 'Please enter portfolio type.',
				'units.required' => 'Please enter units under management.',
				'address.required' => 'Please enter address.',
				'state.required' => 'Please enter state.',
				'city.required' => 'Please enter city.',
				'p_contact_name.required' => 'Please enter point of contact name.',
				'p_contact_no.required' => 'Please enter point of contact no.',
				'p_contact_email.required' => 'Please enter point of contact email.'
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('company_name')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('company_name')));die;
				}
				if($errors->first('email')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('email')));die;
				}
				if($errors->first('contact')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('contact')));die;
				}
				if($errors->first('portfolio_type')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('portfolio_type')));die;
				}
				if($errors->first('units')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('units')));die;
				}
				if($errors->first('address')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('address')));die;
				}
				if($errors->first('state')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('state')));die;
				}
				if($errors->first('city')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('city')));die;
				}
				if($errors->first('p_contact_name')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('p_contact_name')));die;
				}
				if($errors->first('p_contact_no')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('p_contact_no')));die;
				}
				if($errors->first('p_contact_email')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('p_contact_email')));die;
				}
			}else{
				if(strlen($request->contact) != 10){
					return json_encode(array('heading'=>'Error','msg'=>'Invalid mobile no'));die;
				}
				if(!self::$User->ExistingRecord($request->input('email'))){
					if(!self::$User->ExistingMobileRecord($request->input('mobile'))){
						$tempPass = rand(10001,99999);
						$setData['company_name'] = $request->input('company_name');
						$setData['slug'] = $this->builtSlug($request->input('company_name'));
						$setData['email'] = strtolower($request->input('email'));
						$setData['mobile'] = $request->input('contact');
						$setData['country_code'] = $request->input('country_code');
						$setData['country'] = 231;
						$setData['state'] = $request->input('state');
						$setData['city'] = $request->input('city');
						$setData['address'] = $request->input('address');
						$setData['p_contact_name'] = $request->input('p_contact_name');
						$setData['p_contact_no'] = $request->input('p_contact_no');
						$setData['p_contact_email'] = strtolower($request->input('p_contact_email'));
						$setData['portfolio_type'] = $request->input('portfolio_type');
						$setData['units'] = $request->input('units');
						$setData['credits'] = 0;
						$setData['status'] = 2;
						$setData['featured'] = 2;
						$setData['temp_pass'] = $tempPass;
						$setData['password'] = password_hash($tempPass,PASSWORD_BCRYPT);
						$setData['seo_title'] = $request->input('company_name');
						$setData['seo_description'] = $request->input('company_name');
						$setData['seo_keywords'] = $request->input('company_name');
						$record = self::$User->CreateRecord($setData);
						
						$userData = self::$User->where('id',$record->id)->first();
						if($request->input('email') != ''){
							$data = [
							'userData' => $userData,
							'subject' => 'Welcome to Propertifi! Please Verify Your Email Address',
							];
							Mail::to([strtolower(trim($request->email))])->bcc([strtolower(env('BCC')),strtolower(env('BCC2')),strtolower(env('BCC3')),strtolower(env('BCC4')),strtolower(env('BCC5'))])->send(new EmailvarificationMail($data));
						}
						//session(['admin_id' => $record->id, 'admin_email' => $userData->email, 'admin_profile' => $userData, 'admin_type' => $userData->type, 'admin_name' => $userData->name]);
						echo json_encode(array('heading'=>'Success','msg'=>''));
					}else{
						return json_encode(array('heading'=>'Error','msg'=>'Mobile no already exist.'));die;
					}
				}else{
					return json_encode(array('heading'=>'Error','msg'=>'Email address already exist.'));die;
				}
			}
	}
	# loginWithUsernamePasword
    public function loginWithUsernamePasword(Request $request){
		
        if($request->session()->has('admin_email')){return redirect('/admin/dashboard/');}
        $cookieUsername = Cookie::get('cookieUsername');
        $cookiePassword = Cookie::get('cookiePassword');
        return view('/admin/login_username_password',compact('cookieUsername','cookiePassword'));
    }
	# admin dashboard page
    public function admin_login_by_username(Request $request){
		$validator = Validator::make($request->all(), [
			'email' => 'required|email',
			'password' => 'required',
		],[
		'email.required' => 'Please enter your email address.',
		'email.email' => 'Please enter valid email address.',
		'password.required' => 'Please enter your password.'
		]);

		if($validator->fails()){
			 $errors = $validator->errors();
			if($errors->first('email')){
				echo json_encode(array('heading'=>'Error Email','msg'=>$errors->first('email')));
			}else if($errors->first('password')){
				echo json_encode(array('heading'=>'Error Password','msg'=>$errors->first('password')));
			}
		}else{
			if(isset($request->reminderMe) && $request->reminderMe == 1){
                Cookie::queue('cookieUsername', $request->username, 5000);
                Cookie::queue('cookiePassword', $request->password, 5000);
            }else{
                Cookie::queue('cookieUsername', '', 5000);
                Cookie::queue('cookiePassword', '', 5000);
            }

			$User = self::$UserModel->where(array('email' => $request->email))->first();
			if($User){
				if($User->type == 'Admin' || $User->type == 'Account' || $User->type == 'Agent' || $User->type == 'User' || $User->type == 'AccountManager'){
					$PasswordMatch = password_verify($request->password, $User->password);
					if(!$PasswordMatch){
						echo json_encode(array('heading'=>'Error Account','msg'=>'Username and password incorrect'));
					}else{
						session(['admin_id' => $User->id, 'admin_email' => $User->email, 'admin_profile' => $User, 'admin_type' => $User->type, 'admin_name' => $User->name]);
						echo json_encode(array('heading'=>'Success','msg'=>''));
					}
				}else{
					echo json_encode(array('heading'=>'Error Account','msg'=>'Username and password incorrect'));
				}
			}else{
				echo json_encode(array('heading'=>'Error Account','msg'=>'Username and password incorrect'));
			}
		}
    }
    # admin dashboard page
    public function admin_login(Request $request){
		$validator = Validator::make($request->all(), [
			'mobile' => 'required',
		],[
		'mobile.required' => 'Please enter your mobile or email.',
		]);
		if($validator->fails()){
			 $errors = $validator->errors();
			if($errors->first('mobile')){
				echo json_encode(array('heading'=>'Error','msg'=>$errors->first('mobile')));
			}
		}else{
			$User = self::$UserModel->where('email',$request->post('mobile'))->orWhere('mobile',$request->post('mobile'))->first();
			if($User){
				if($User->status == 1){
					$otp = mt_rand(1001,9999);
					if(is_numeric($request->mobile)){
						$userPhone = $request->mobile;
						if($request->mobile == '1111111111'){
							$userPhone = '7737406899';
						}
						if($request->mobile == '2222222222'){
							$userPhone = '9829364516';
						}
						$templateID = '6788ac4bd6fc05441563c062';
						$messageData = ['mobiles' => $User->country_code.$userPhone,'var1' => $otp];
						$response = self::$SendSms->SendSMS($templateID,$messageData);
					}else{
						$data = [
							'otp' => $otp,
							'subject' => 'Verification Code',
						];
						Mail::to([strtolower(trim($request->mobile))])->bcc([strtolower(env('BCC')),strtolower(env('BCC2')),strtolower(env('BCC3')),strtolower(env('BCC4')),strtolower(env('BCC5'))])->send(new OtpMail($data));
					}
						
					self::$UserModel->where('id', $User->id)->update(array('otp' => $otp));
					echo json_encode(array('heading'=>'Success','msg'=>$User->id));
				}else{
					echo json_encode(array('heading'=>'Error Account','msg'=>'Your Account is disabled. Please drop a mail to help@propertifi.com for further help.'));
				}
			}else{
				echo json_encode(array('heading'=>'Error Account','msg'=>'Mobile or Email Does Not Exists'));
			}
		}
		exit;
    }
	# admin dashboard page
    public function admin_otp_login(Request $request){
		$validator = Validator::make($request->all(), [
			'u_id' => 'required|numeric',
			'otp' => 'required|numeric',
		],[
		'u_id.required' => 'Please enter u_id.',
		'otp.required' => 'Please enter your OTP.',
		'otp.numeric' => 'Please enter valid OTP.',
		]);
		if($validator->fails()){
			 $errors = $validator->errors();
			if($errors->first('u_id')){
				echo json_encode(array('heading'=>'Error','msg'=>$errors->first('u_id')));
			}else if($errors->first('otp')){
				echo json_encode(array('heading'=>'Error OTP','msg'=>$errors->first('otp')));
			}
		}else{
			$valid_users = array('Admin','Agent','AccountManager');
			$User = self::$UserModel->where(array('id' => $request->u_id,'otp' => $request->otp))->whereIn('type',$valid_users)->first();
			if($User){
				//session(['admin_id' => $User->id, 'admin_email' => $User->email, 'admin_profile' => $User, 'admin_type' => $User->type, 'admin_name' => $User->name]);
				Session::put('admin_id',$User->id);
				Session::put('admin_email',$User->email);
				Session::put('admin_profile',$User);
				Session::put('admin_type',$User->type);
				Session::put('admin_name',$User->name);
 				Session::save();				
				self::$UserModel->where('id', $User->id)->update(array('otp' => NULL));				
				echo json_encode(array('heading'=>'Success','msg'=>''));
			}else{
				echo json_encode(array('heading'=>'Error Account','msg'=>'Please enter correct OTP'));
			}
		}
		exit;
    }

    

}
