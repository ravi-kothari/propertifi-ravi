<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Settings;
use App\RouteHelper;
use App\Models\TokenHelper;
use App\Models\Responses;
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
use App\Mail\SignupMail;
use App\Mail\EmailvarificationMail;
use App\Mail\AccountActiveMail;
use App\Mail\ResetPasswordMail;

class AgentsController extends Controller
{
	private static $User;
    private static $TokenHelper;
	private static $State;
	private static $Cities;
	private static $Settings;
	public function __construct(){
		self::$User = new User();
        self::$TokenHelper = new TokenHelper();
		self::$State = new State();
		self::$Cities = new Cities();
		self::$Settings = new Settings();
	}

    #admin dashboard page
    public function getList(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),3,'view')){return redirect('/admin/dashboard/');}
		$settings = self::$Settings->where('id', 1)->first();
		$agentCount = self::$User->where('status', '!=', 3)->where('type', 'Agent')->count();
        return view('/admin/agents/index',['settings' => $settings,'agentCount' => $agentCount]);
    }
    public function listPaginate(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        $query = self::$User->where('status', '!=', 3)->where('type', 'Agent');
		if($request->input('title')  && $request->input('title') != ""){
            $SearchKeyword = $request->input('title');
            $query->where('company_name', 'like', '%'.$SearchKeyword.'%');
		}
		if($request->input('email')  && $request->input('email') != ""){
            $query->where('email', 'like', '%'.$request->input('email').'%');
		}
		if($request->input('status')  && $request->input('status') != ""){
            $query->where('status', $request->input('status'));
		}
		$records =  $query->orderBy('id', 'DESC')->latest()->paginate(100);
		foreach($records as $key => $record){
			
			$record->mobile = '+'.$record->country_code.$record->mobile;
			/*$slug = $this->builtSlug($record->company_name);
			$checkSlug = self::$User->where('slug', $slug)->count();
			if($checkSlug > 0){
				$slug = $slug.'-'.rand(1001,9999);
			}
			self::$User->where('id',$record->id)->update(['slug' => $slug,'seo_title' => $record->company_name,'seo_description' => $record->company_name,'seo_keywords' => $record->company_name]);*/
		}
        return view('/admin/agents/paginate',compact('records'));
    }
	public function resendVarificationEmail(Request $request){
		
		$agentData = self::$User->where('id',$request->id)->first();
		if($agentData->email_varification == 2){
			if($agentData->email != ''){
				$data = [
				'userData' => $agentData,
				'subject' => 'Welcome to Propertifi! Please Verify Your Email Address',
				];
				Mail::to([strtolower(trim($agentData->email))])->bcc([strtolower(env('BCC')),strtolower(env('BCC2')),strtolower(env('BCC3')),strtolower(env('BCC4')),strtolower(env('BCC5'))])->send(new EmailvarificationMail($data));
				echo 'Success'; die;
			}
		}
		echo 'Fail'; die;
	}
	public function exportPropertyManagers(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        $query = self::$User->where('status', '!=', 3)->where('type', 'Agent');
		if($request->input('title')  && $request->input('title') != ""){
            $SearchKeyword = $request->input('title');
            $query->where('company_name', 'like', '%'.$SearchKeyword.'%');
		}
		if($request->input('status')  && $request->input('status') != ""){
            $query->where('status', $request->input('status'));
		}
		$records =  $query->orderBy('id', 'DESC')->get();
		
		$delimiter = ",";
		$filename = "property_managers_" . date('d_F_Y') . ".csv";
		
		$destination = "storage/csv/".$filename;
		//create a file pointer
		$f = fopen($destination,"w");
		
		//set column headers
		$fields = array(
						'S.No', 
						'Company Name',
						'Company Email',
						'Address',
						'Contact No.',
						'Point of Name',
						'Point of Contact',
						'Credits',
						'Varification',
						'Date'
						);
		fputcsv($f, $fields, $delimiter);
		
		
		foreach($records as $key => $record){
			
			$varified = 'Verified';
			if($record->email_varification == 2){
			$varified = 'Pending';
			}
	
			$record->mobile = '+'.$record->country_code.$record->mobile;
			
			$lineData = array(
							$key+1,
							$record->company_name,
							$record->email,
							$record->address,
							'+'.$record->country_code.$record->mobile,
							$record->p_contact_name,
							$record->p_contact_no,
							$record->credits,
							$varified,
							$record->created_at                          
						);
	
		fputcsv($f, $lineData, $delimiter);
		}
		$lineData2 = array('','');						
		fputcsv($f, $lineData2, $delimiter);                     
		
		fclose ($f);

		echo env('APP_URL').$destination;		
		exit;
    }
	#add new
    public function addPage(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),3,'addd')){return redirect('/admin/dashboard/');}
		if($request->input()){
			$validator = Validator::make($request->all(), [
                'company_name' => 'required',
				'email' => 'required|email',
				'contact' => 'required|numeric',
				'password' => 'required',
            ],[
                'company_name.required' => 'Please enter company name.',
				'email.required' => 'Please enter email.',
				'contact.required' => 'Please enter contact.',
				'password.required' => 'Please enter password.',
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
				if($errors->first('password')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('password')));die;
				}
			}else{
				if(strlen($request->contact) != 10){
					return json_encode(array('heading'=>'Error','msg'=>'Invalid mobile no'));die;
				}
                if(!self::$User->ExistingRecord($request->input('email'))){
					if(!self::$User->ExistingMobileRecord($request->input('mobile'))){
						# image upload
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
							}
						}
						$featured = 2;
						if($request->input('featured')){
							$featured = 1;
						}
						$setData['company_name'] = $request->input('company_name');
						$setData['slug'] = $this->builtSlug($request->input('company_name'));
						$setData['email'] = strtolower($request->input('email'));
						$setData['mobile'] = $request->input('contact');
						$setData['country_code'] = $request->input('country_code');
						$setData['address'] = $request->input('address');
						$setData['credits'] = 0;
						$setData['country'] = 231;
						$setData['state'] = $request->input('state');
						$setData['city'] = $request->input('city');
						$setData['zipcode'] = $request->input('zipcode');
						$setData['email_varification'] = $request->input('email_varification');
						$setData['about'] = $request->input('about');
						$setData['p_contact_name'] = $request->input('p_contact_name');
						$setData['p_contact_no'] = $request->input('p_contact_no');
						$setData['p_contact_email'] = strtolower($request->input('p_contact_email'));
						$setData['status'] = $request->input('status');
						$setData['position'] = $request->input('position');
						$setData['portfolio_type'] = $request->input('portfolio_type');
						$setData['units'] = $request->input('units');
						$setData['seo_title'] = $request->input('company_name');
						$setData['seo_description'] = $request->input('company_name');
						$setData['seo_keywords'] = $request->input('company_name');
						/*$setData['single_family'] = $request->input('single_family');
						$setData['multi_family'] = $request->input('multi_family');
						$setData['association_property'] = $request->input('association_property');
						$setData['commercial_property'] = $request->input('commercial_property');*/
						$setData['featured'] = $featured;
						$setData['temp_pass'] = $request->post('password');
						$setData['password'] = password_hash($request->post('password'),PASSWORD_BCRYPT);
						
						$record = self::$User->CreateRecord($setData);
						
						if($request->input('email') != ''){
							$userData = self::$User->where('id',$record->id)->first();
							$data = [
									'userData' => $userData,
									'subject' => 'Property Agent Registration',
								];
							Mail::to([strtolower(trim($request->email))])->bcc([strtolower(env('BCC')),strtolower(env('BCC2')),strtolower(env('BCC3')),strtolower(env('BCC4')),strtolower(env('BCC5'))])->send(new SignupMail($data));
						}
						
						 echo json_encode(array('heading'=>'Success','msg'=>'Property manager added successfully'));die;
						 
					}else{
						return json_encode(array('heading'=>'Error','msg'=>'Mobile no already exist.'));die;
					}
					
                }else{
					return json_encode(array('heading'=>'Error','msg'=>'Email address already exist.'));die;
				}
               

			}
		}
		$states = self::$State->where(array('status' => 1,'country_id' => 231))->orderBy('state','ASC')->get();
		return view('/admin/agents/add-page',['states' => $states]);
    }
	#edit
    public function editPage(Request $request, $row_id){
		$RowID =  base64_decode($row_id);
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),3,'edit')){return redirect('/admin/dashboard/');}

        if($request->input()){
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
                if(self::$User->ExistingRecordUpdate($request->input('email'), $RowID)){
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
					$featured = 2;
					if($request->input('featured')){
						$featured = 1;
					}
                    $setData['id'] =  $RowID;
                    $setData['company_name'] = $request->input('company_name');
					$setData['email'] = strtolower($request->input('email'));
					$setData['mobile'] = $request->input('contact');
					$setData['country_code'] = $request->input('country_code');
					$setData['address'] = $request->input('address');
					$setData['country'] = 231;
					$setData['state'] = $request->input('state');
					$setData['city'] = $request->input('city');
					$setData['zipcode'] = $request->input('zipcode');
					$setData['email_varification'] = $request->input('email_varification');
					$setData['about'] = $request->input('about');
					$setData['p_contact_name'] = $request->input('p_contact_name');
					$setData['p_contact_no'] = $request->input('p_contact_no');
					$setData['p_contact_email'] = strtolower($request->input('p_contact_email'));
					$setData['status'] = $request->input('status');
					$setData['position'] = $request->input('position');
					$setData['portfolio_type'] = $request->input('portfolio_type');
					$setData['units'] = $request->input('units');
					/*$setData['single_family'] = $request->input('single_family');
					$setData['multi_family'] = $request->input('multi_family');
					$setData['association_property'] = $request->input('association_property');
					$setData['commercial_property'] = $request->input('commercial_property');*/
					$setData['featured'] = $featured;
					if($request->post('password') != ''){
						$setData['temp_pass'] = $request->post('password');
                    	$setData['password'] = password_hash($request->post('password'),PASSWORD_BCRYPT);
					}
					
					$setData['seo_title'] = $request->input('seo_title');
					$setData['seo_description'] = $request->input('seo_description');
					$setData['seo_keywords'] = $request->input('seo_keywords');
					
                    self::$User->UpdateRecord($setData);
					
					/*$userData = self::$User->where('id',$RowID)->first();
					$data = [
					'userData' => $userData,
					'subject' => 'Welcome to Propertifi! Please Verify Your Email Address',
					];
					Mail::to([strtolower(trim($request->input('email')))])->bcc([strtolower(env('BCC')),strtolower(env('BCC2')),strtolower(env('BCC3')),strtolower(env('BCC4')),strtolower(env('BCC5'))])->send(new EmailvarificationMail($data));*/

					
					
                }
                echo json_encode(array('heading'=>'Success','msg'=>'Property manager information updated successfully'));die;
			}
		}
		$rowData = self::$User->where(array('id' => $RowID))->first();
        if(isset($rowData->id)){
			$cities = [];
			$states = self::$State->where(array('status' => 1,'country_id' => 231))->orderBy('state','ASC')->get();
			if($rowData->state > 0){
				$cities = self::$Cities->where(array('status' => 1,'state_id' => $rowData->state))->orderBy('city','ASC')->get();
			}
            return view('/admin/agents/edit-page',compact('rowData','row_id','states','cities'));
        }else{
            return redirect('/admin/property-managers');
        }
    }
	public function getCities(Request $request){
		$cities = self::$Cities->where(array('status' => 1,'state_id' => $request->stateID))->orderBy('city','ASC')->get();
		$html = '<option value="">Select City</option>';
		foreach($cities as $key => $city):
		$html .= '<option value="'.$city->id.'">'.$city->city.'</option>';
		endforeach;
		echo $html; die;
	}
	#edit 
    public function viewPage(Request $request, $row_id){
		$RowID =  base64_decode($row_id);
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),3,'view')){return redirect('/admin/dashboard/');}

		$rowData = self::$User->where(array('id' => $RowID))->first();
        if(isset($rowData->id)){
            return view('/admin/agents/view-page',compact('rowData','row_id'));
        }else{
            return redirect('/admin/property-managers');
        }
    }
	public function deleteAgent(Request $request){
		foreach($request->ids as $key => $id){
			self::$User->where('id',$id)->update(['status' => 3]);
		}
		echo 'Success';
		die;
	}

}
