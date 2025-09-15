<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Settings;
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
use App\Models\Cities;

class ProfileController extends Controller
{
	private static $UserModel;
    private static $TokenHelper;
	private static $Settings;
	private static $State;
	private static $Cities;
	 
	public function __construct(){
		self::$UserModel = new User();
        self::$TokenHelper = new TokenHelper();
		self::$Settings = new Settings();
		self::$State = new State();
		self::$Cities = new Cities();
	}
	# admin profile page
    public function updateProfile (Request $request){
		if(!$request->session()->has('admin_email')){ return redirect('/admin/'); }
		$userData = self::$UserModel->where(array('id' => $request->session()->get('admin_id')))->first();
        return view('/admin/profile/update_profile',compact('userData'));
    }
    # agentProfile
    public function agentProfile (Request $request){
		if(!$request->session()->has('admin_email')){ return redirect('/admin/'); }
		$rowData = self::$UserModel->where(array('id' => $request->session()->get('admin_id')))->first();
        return view('/admin/profile/agent_profile',compact('rowData'));
    }
	# admin profile page
    public function editAgentProfile (Request $request){
		if(!$request->session()->has('admin_email')){ return redirect('/admin/'); }
		$rowData = self::$UserModel->where(array('id' => $request->session()->get('admin_id')))->first();
		$cities = [];
		$states = self::$State->where(array('status' => 1,'country_id' => 231))->orderBy('state','ASC')->get();
		if($rowData->state > 0){
			$cities = self::$Cities->where(array('status' => 1,'state_id' => $rowData->state))->orderBy('city','ASC')->get();
		}
        return view('/admin/profile/edit_agent_profile',compact('rowData','cities','states'));
    }
	# settings
    public function settings (Request $request){
		if(!$request->session()->has('admin_email')){ return redirect('/admin/'); }
		$setting  = self::$Settings->where(array('id' => 1))->first();
        return view('/admin/profile/settings',compact('setting'));
    }
	public function saveSetting(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		
			$validator = Validator::make($request->all(), [
                'company_name' => 'required',
				'email' => 'required|email',
				'mobile' => 'required|numeric',
            ],[
                'company_name.required' => 'Please enter company name.',
				'email.required' => 'Please enter email.',
				'mobile.required' => 'Please enter mobile.',
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('company_name')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('company_name')));die;
				}
				if($errors->first('email')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('email')));die;
				}
				if($errors->first('mobile')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('mobile')));die;
				}
			}else{
				if(strlen($request->mobile) != 10){
					return json_encode(array('heading'=>'Error','msg'=>'Invalid mobile no'));die;
				}
                # Image upload
				if(isset($request->file) && $request->file->extension() != ""){
					$validator = Validator::make($request->all(), [
						'file' => 'required|image|mimes:jpeg,png,jpg|max:2048'
					]);
					if($validator->fails()){
						$errors = $validator->errors();
						return json_encode(array('heading'=>'Error','msg'=>$errors->first('file')));die;
					}else{
						$actual_image_name = time().'.'.$request->file->extension();
						$destination = base_path().'/public/admin/images/profile/';
						$request->file->move($destination, $actual_image_name);
						$setData['logo'] = $actual_image_name;

						if($request->input('old_file') != ""){
							if(file_exists($destination.$request->input('old_file'))){
								unlink($destination.$request->input('old_file'));
							}
						}

					}
				}
				
				$setData['id'] =  1;
				$setData['company_name'] = $request->input('company_name');
				$setData['email'] = $request->input('email');
				$setData['mobile'] = $request->input('mobile');
				
				$setData['business_address'] = $request->input('business_address');
				$setData['footer_content'] = $request->input('footer_content');
				//$setData['per_credit_amt'] = $request->input('per_credit_amt');

				self::$Settings->UpdateRecord($setData);
                echo json_encode(array('heading'=>'Success','msg'=>'Settings updated successfully'));die;
			}
		
   
    }
	public function saveCreditAmt(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		
			$validator = Validator::make($request->all(), [
				'per_credit_amt' => 'required|numeric'
            ],[
				'per_credit_amt.required' => 'Please enter Per Credit Amount.'
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('per_credit_amt')){
                    return json_encode(array('success'=>false,'msg'=>$errors->first('per_credit_amt')));die;
				}
			}else{

				$setData['id'] =  1;
				$setData['per_credit_amt'] = $request->input('per_credit_amt');
				self::$Settings->UpdateRecord($setData);
                echo json_encode(array('success'=>true,'msg'=>'Settings updated successfully'));die;
			}
		
   
    }
	public function saveAgentProfile(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		
			$validator = Validator::make($request->all(), [
                'company_name' => 'required',
				'email' => 'required|email',
				'contact' => 'required|numeric',
            ],[
                'company_name.required' => 'Please enter company name.',
				'email.required' => 'Please enter email.',
				'contact.required' => 'Please enter contact.',
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
			}else{
				if(strlen($request->contact) != 10){
					return json_encode(array('heading'=>'Error','msg'=>'Invalid mobile no'));die;
				}
                //profile image
                if(self::$UserModel->ExistingRecordUpdate($request->input('email'), $request->session()->get('admin_id'))){
                    echo json_encode(array('heading'=>'Error','msg'=>'Email already exists.'));die;
                }else{
					# Image upload
                    if(isset($request->file) && $request->file->extension() != ""){
                        $validator = Validator::make($request->all(), [
                            'file' => 'required|image|mimes:jpeg,png,jpg|max:2048'
                        ]);
                        if($validator->fails()){
                            $errors = $validator->errors();
                            return json_encode(array('heading'=>'Error','msg'=>$errors->first('file')));die;
                        }else{
                            $actual_image_name = time().'.'.$request->file->extension();
                            $destination = base_path().'/public/img/users/';
                            $request->file->move($destination, $actual_image_name);
                            $setData['photo'] = $actual_image_name;

                            if($request->input('old_file') != ""){
                                if(file_exists($destination.$request->input('old_file'))){
                                    unlink($destination.$request->input('old_file'));
                                }
                            }

                        }
                    }
					
                    $setData['id'] =  $request->session()->get('admin_id');
                    $setData['company_name'] = $request->input('company_name');
					//$setData['email'] = $request->input('email');
					//$setData['mobile'] = $request->input('contact');
					$setData['address'] = $request->input('address');
					$setData['country'] = 231;
					$setData['state'] = $request->input('state');
					$setData['city'] = $request->input('city');
					$setData['about'] = $request->input('about');
					$setData['zipcode'] = $request->input('zipcode');
					$setData['p_contact_name'] = $request->input('p_contact_name');
					$setData['p_contact_no'] = $request->input('p_contact_no');
					$setData['p_contact_email'] = $request->input('p_contact_email');
					$setData['position'] = $request->input('position');
					$setData['single_family'] = $request->input('single_family');
					$setData['multi_family'] = $request->input('multi_family');
					$setData['association_property'] = $request->input('association_property');
					$setData['commercial_property'] = $request->input('commercial_property');
					$setData['portfolio_type'] = $request->input('portfolio_type');
					$setData['units'] = $request->input('units');

                    self::$UserModel->UpdateRecord($setData);
                }
                echo json_encode(array('heading'=>'Success','msg'=>'Profile updated successfully'));die;
			}
		
   
    }
    # update profile page
    public function saveProfile(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		$validator = Validator::make($request->all(), [
			'name' => 'required|regex:/^[\pL\s\-]+$/u'
		],[
		'name.required' => 'Please enter your name.',
		'name.alpha' => 'Please enter valid name.'
		]);
		if($validator->fails()){
			$errors = $validator->errors();
            return json_encode(array('heading'=>'Error','msg'=>$errors->first('name')));die;
		}else{
			# profile pic upload
			if(isset($request->banner) && $request->banner->extension() != ""){
                $validator = Validator::make($request->all(), [
                    'banner' => 'required|image|mimes:jpeg,png,jpg|max:2048'
                ]);
                if($validator->fails()){
                    $errors = $validator->errors();
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('banner')));die;
                }else{
                    $actual_image_name = time().'.'.$request->banner->extension();
                    $destination = base_path().'/public/admin/images/profile/';
                    $request->banner->move($destination, $actual_image_name);
                    if($request->input('old_banner') != ""){
                        if(file_exists($destination.$request->input('old_banner'))){
                            unlink($destination.$request->input('old_banner'));
                        }
                    }
                }
			}else{
				$actual_image_name = $request->input('old_banner');
			}
			
			$check = self::$UserModel->where('mobile',$request->input('mobile'))->where('id','!=',$request->session()->get('admin_id'))->count();
			if($check == 0){
				self::$UserModel->where(array('id' => $request->session()->get('admin_id')))->update(array('photo' => $actual_image_name, 'name' => trim($request->input('name')),'mobile' => trim($request->input('mobile'))));
				return json_encode(array('heading'=>'Success','msg'=>'Profile updated successfully'));die;
			}else{
				return json_encode(array('heading'=>'Error','msg'=>'Phone no already exist'));die;
			}
		}
    }

    # change password page
    public function changePassword(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        return view('/admin/profile/change_password');
    }

    # update password
    public function updatePassword(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		$validator = Validator::make($request->all(), [
			'password' => ['required','min:6'],
			'confirm_password' => 'required|same:password'
		],[
		'password.required' => 'Please enter your password.',
		'confirm_password.required' => 'Please enter your confirm password.',
		]);
		if($validator->fails()){
			$errors = $validator->errors();
			if($errors->first('current_password')){
                return json_encode(array('heading'=>'Error','msg'=>$errors->first('current_password')));die;
			}else if($errors->first('password')){
				return json_encode(array('heading'=>'Error','msg'=>$errors->first('password')));die;
			}else if($errors->first('confirm_password')){
				return json_encode(array('heading'=>'Error','msg'=>$errors->first('confirm_password')));die;
			}
		}else{
            $User = self::$UserModel->where(array('id' => $request->session()->has('admin_id')))->first();
			if($User){
                $PasswordMatch = password_verify($request->current_password, $User->password);
                if(!$PasswordMatch){
                    echo json_encode(array('heading'=>'Error','msg'=>'Old password is incorrect'));
                }else{
                    $Password = password_hash($request->post('password'),PASSWORD_BCRYPT);
                    self::$UserModel->where(array('id' => $request->session()->has('admin_id')))->update(array('password' => $Password));
                    return json_encode(array('heading'=>'Success','msg'=>'Password updated successfully'));die;
                }
            }else{
                echo json_encode(array('heading'=>'Error','msg'=>'invalid User.'));
            }
		}
    }
}
