<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Faqs;
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
use App\Models\User;

class FaqsController extends Controller
{
	private static $Faqs;
	private static $User;
    private static $TokenHelper;
	public function __construct(){
		self::$Faqs = new Faqs();
        self::$TokenHelper = new TokenHelper();
		self::$User = new User();
	}

    #admin dashboard page
    public function getList(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),7,'view')){return redirect('/admin/dashboard/');}
		$userData = self::$User->where(array('id' => $request->session()->get('admin_id')))->first();
        return view('/admin/faqs/index',compact('userData'));
    }
    public function listPaginate(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		
		$userData = self::$User->where(array('id' => $request->session()->get('admin_id')))->first();
		
        $query = self::$Faqs->where('status', '!=', 3);
		
		if($userData->type == 'Agent'){
			$query->where('type', 'Property Manager');
		}
		
		if($request->input('title')  && $request->input('title') != ""){
            $SearchKeyword = $request->input('title');
            $query->where('question', 'like', '%'.$SearchKeyword.'%');
		}
		if($request->input('status')  && $request->input('status') != ""){
            $query->where('status', $request->input('status'));
		}
		if($request->input('type')  && $request->input('type') != ""){
            $query->where('type', $request->input('type'));
		}
		$records =  $query->orderBy('id', 'DESC')->latest()->paginate(100);
        return view('/admin/faqs/paginate',compact('records','userData'));
    }
	#add new
    public function addPage(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),7,'addd')){return redirect('/admin/dashboard/');}
		if($request->input()){
			$validator = Validator::make($request->all(), [
                'question' => 'required',
				'answer' => 'required',
            ],[
                'question.required' => 'Please enter question.',
				'answer.required' => 'Please enter answer.',
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('question')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('question')));die;
				}
				if($errors->first('answer')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('answer')));die;
				}
			}else{

				$setData['question'] = $request->input('question');
				$setData['answer'] = $request->input('answer');
				$setData['type'] = $request->input('type');
				$setData['status'] = 1;
				
				$record = self::$Faqs->CreateRecord($setData);
                echo json_encode(array('heading'=>'Success','msg'=>'Faq added successfully'));die;

			}
		}
		return view('/admin/faqs/add-page');
    }
	#edit
    public function editPage(Request $request, $row_id){
		$RowID =  base64_decode($row_id);
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		$permission = 'edit';
		if(!$this->checkPermission($request->session()->get('admin_id'),7,'edit')){$permission = 'view';}

        if($request->input()){
			$validator = Validator::make($request->all(), [
                'question' => 'required',
				'answer' => 'required',
            ],[
                'question.required' => 'Please enter question.',
				'answer.required' => 'Please enter answer.',
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('question')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('question')));die;
				}
				if($errors->first('answer')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('answer')));die;
				}
			}else{

				$setData['id'] =  $RowID;
				$setData['question'] = $request->input('question');
				$setData['answer'] = $request->input('answer');
				$setData['status'] = $request->input('status');
				$setData['type'] = $request->input('type');

				self::$Faqs->UpdateRecord($setData);
			}
			echo json_encode(array('heading'=>'Success','msg'=>'Faq updated successfully'));die;
			}
		$rowData = self::$Faqs->where(array('id' => $RowID))->first();
        if(isset($rowData->id)){
            return view('/admin/faqs/edit-page',compact('rowData','row_id','permission'));
        }else{
            return redirect('/admin/faqs');
        }
    }

}
