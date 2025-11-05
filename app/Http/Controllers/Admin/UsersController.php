<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Settings;
use App\RouteHelper;
use App\Models\TokenHelper;
use App\Models\Responses;
use App\Models\Roles;
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

class UsersController extends Controller
{
	private static $User;
    private static $TokenHelper;
	private static $State;
	private static $Cities;
	private static $Settings;
	private static $Roles;
	public function __construct(){
		self::$User = new User();
        self::$TokenHelper = new TokenHelper();
		self::$State = new State();
		self::$Cities = new Cities();
		self::$Settings = new Settings();
		self::$Roles = new Roles();
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
		
		$settings = self::$Settings->where('id', 1)->first();
		$roles = self::$Roles->where(array('status' => 1))->orderBy('title','ASC')->get();
        return view('/admin/users/index',['settings' => $settings,'roles' => $roles,'agentID' => $agentID]);
    }
    public function listPaginate(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        $query = self::$User->where('status', '!=', 3)->where('type', 'User')->where('added_by', $request->agentID);
		if($request->input('title')  && $request->input('title') != ""){
            $SearchKeyword = $request->input('title');
            $query->where('company_name', 'like', '%'.$SearchKeyword.'%');
		}
		if($request->input('status')  && $request->input('status') != ""){
            $query->where('status', $request->input('status'));
		}
		if($request->input('role_id')  && $request->input('role_id') != ""){
            $query->where('role_id', $request->input('role_id'));
		}
		$records =  $query->orderBy('id', 'DESC')->latest()->paginate(100);
		foreach($records as $key => $record){
			$roleData = self::$Roles->where(array('id' => $record->role_id))->first();
			$record->mobile = '+'.$record->country_code.$record->mobile;
			$record->user_role = isset($roleData->id) ? $roleData->title : 'N/A';
		}
        return view('/admin/users/paginate',compact('records'));
    }
	#add new
    public function addPage(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
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
					
					$phoneExist = self::$User->where('mobile',$request->input('contact'))->count();
					if($phoneExist > 0){
						return json_encode(array('heading'=>'Error','msg'=>'Mobile no already exist.'));die;
					}
					
                    $setData['type'] = 'User';
					$setData['added_by'] = $request->session()->get('admin_id');
					$setData['role_id'] = $request->input('role_id');
					$setData['name'] = $request->input('company_name');
					$setData['company_name'] = $request->input('company_name');
					$setData['email'] = $request->input('email');
					$setData['mobile'] = $request->input('contact');
					$setData['country_code'] = $request->input('country_code');
					$setData['credits'] = 0;
					$setData['country'] = 231;
					$setData['email_varification'] = $request->input('email_varification');
					$setData['status'] = $request->input('status');
					$setData['password'] = password_hash($request->post('password'),PASSWORD_BCRYPT);
					
                    $record = self::$User->CreateRecord($setData);
					
					/*if($request->input('email') != ''){
						$userData = self::$User->where('id',$record->id)->first();
						$data = [
								'userData' => $userData,
								'subject' => 'Account Manager Registration',
							];
						Mail::to([strtolower(trim($request->email))])->bcc([strtolower(env('BCC'))])->send(new SignupMail($data));
					}*/
                }
                echo json_encode(array('heading'=>'Success','msg'=>'User added successfully'));die;

			}
		}
		$roles = self::$Roles->where(array('status' => 1))->orderBy('title','ASC')->get();
		return view('/admin/users/add-page',['roles' => $roles]);
    }
	#edit
    public function editPage(Request $request, $row_id){
		$RowID =  base64_decode($row_id);
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}

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
					
					$phoneExist = self::$User->where('mobile',$request->input('contact'))->where('id','!=',$RowID)->count();
					if($phoneExist > 0){
						return json_encode(array('heading'=>'Error','msg'=>'Mobile no already exist.'));die;
					}
					
                    $setData['id'] =  $RowID;
                    $setData['role_id'] = $request->input('role_id');
					$setData['name'] = $request->input('company_name');
					$setData['company_name'] = $request->input('company_name');
					$setData['email'] = $request->input('email');
					$setData['mobile'] = $request->input('contact');
					$setData['country_code'] = $request->input('country_code');
					$setData['email_varification'] = $request->input('email_varification');
					if($request->post('password') != ''){
                    	$setData['password'] = password_hash($request->post('password'),PASSWORD_BCRYPT);
					}
					
					
                    self::$User->UpdateRecord($setData);
					
					
                }
                echo json_encode(array('heading'=>'Success','msg'=>'User information updated successfully'));die;
			}
		}
		$rowData = self::$User->where(array('id' => $RowID))->first();
        if(isset($rowData->id)){
			$roles = self::$Roles->where(array('status' => 1))->orderBy('title','ASC')->get();
			
            return view('/admin/users/edit-page',compact('rowData','row_id','roles'));
        }else{
            return redirect('/admin/users');
        }
    }

    /**
     * Display verification interface for a PM
     */
    public function showVerification(Request $request, $userId)
    {
        if(!$request->session()->has('admin_email')){
            return redirect('/admin/');
        }

        $user = User::findOrFail($userId);

        return view('admin.users.verification', compact('user'));
    }

    /**
     * Update verification status
     */
    public function updateVerification(Request $request, $userId)
    {
        if(!$request->session()->has('admin_email')){
            return redirect('/admin/');
        }

        $validator = Validator::make($request->all(), [
            'is_verified' => 'required|boolean'
        ]);

        if($validator->fails()){
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $user = User::findOrFail($userId);

        $user->update([
            'is_verified' => $request->is_verified,
            'verified_at' => $request->is_verified ? now() : null
        ]);

        return redirect()->back()->with('success', 'Verification status updated successfully.');
    }

    /**
     * Upload verification document
     */
    public function uploadVerificationDocument(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120' // 5MB max
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = User::findOrFail($userId);

        // Store document in storage/app/verification-documents
        $path = $request->file('document')->store('verification-documents');

        // Add to verification_documents array
        $documents = $user->verification_documents ?? [];
        $documents[] = [
            'path' => $path,
            'original_name' => $request->file('document')->getClientOriginalName(),
            'uploaded_at' => now()->toDateTimeString(),
            'uploaded_by' => $request->session()->get('admin_id')
        ];

        $user->update(['verification_documents' => $documents]);

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully',
            'document' => end($documents)
        ]);
    }

    /**
     * Delete verification document
     */
    public function deleteVerificationDocument(Request $request, $userId, $documentIndex)
    {
        $user = User::findOrFail($userId);
        $documents = $user->verification_documents ?? [];

        if(isset($documents[$documentIndex])){
            // Delete file from storage
            if(file_exists(storage_path('app/' . $documents[$documentIndex]['path']))){
                unlink(storage_path('app/' . $documents[$documentIndex]['path']));
            }

            // Remove from array
            unset($documents[$documentIndex]);
            $documents = array_values($documents); // Re-index array

            $user->update(['verification_documents' => $documents]);

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Document not found'
        ], 404);
    }

    /**
     * Download verification document
     */
    public function downloadVerificationDocument(Request $request, $userId, $documentIndex)
    {
        $user = User::findOrFail($userId);
        $documents = $user->verification_documents ?? [];

        if(isset($documents[$documentIndex])){
            $filePath = storage_path('app/' . $documents[$documentIndex]['path']);

            if(file_exists($filePath)){
                return response()->download($filePath, $documents[$documentIndex]['original_name']);
            }
        }

        return redirect()->back()->with('error', 'Document not found');
    }

}
