<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Questions;
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

class QuestionsController extends Controller
{
	private static $Questions;
    private static $TokenHelper;
	public function __construct(){
		self::$Questions = new Questions();
        self::$TokenHelper = new TokenHelper();
	}

    #admin dashboard page
    public function getList(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),10,'view')){return redirect('/admin/dashboard/');}
        return view('/admin/questions/index');
    }
    public function listPaginate(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        $query = self::$Questions->where('status', '!=', 3);
		if($request->input('question')  && $request->input('question') != ""){
            $SearchKeyword = $request->input('question');
            $query->where('question', 'like', '%'.$SearchKeyword.'%');
		}
		if($request->input('status')  && $request->input('status') != ""){
            $query->where('status', $request->input('status'));
		}
		if($request->input('category')  && $request->input('category') != ""){
            $query->where('category', $request->input('category'));
		}
		$records =  $query->orderBy('id', 'ASC')->latest()->paginate(100);
		foreach($records as $key => $record){
			$category = 'N/A';
			if($record->category == 1){
				$category = 'Single Family';
			}else if($record->category == 2){
				$category = 'Multi Family';
			}else if($record->category == 3){
				$category = 'Association';
			}else if($record->category == 8){
				$category = 'Commercial';
			}
			$record->category = $category;
		}
        return view('/admin/questions/paginate',compact('records'));
    }
	#add new
    public function addPage(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),10,'addd')){return redirect('/admin/dashboard/');}
		if($request->input()){
			$validator = Validator::make($request->all(), [
                'category' => 'required',
				'question' => 'required',
            ],[
                'category.required' => 'Please enter category.',
				'question.required' => 'Please enter question.',
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('category')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('category')));die;
				}
				if($errors->first('question')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('question')));die;
				}
			}else{

				$setData['category'] = $request->input('category');
				$setData['question'] = $request->input('question');
				$setData['type'] = $request->input('type');
				$setData['option_1'] = $request->input('option_1');
				$setData['option_2'] = $request->input('option_2');
				$setData['option_3'] = $request->input('option_3');
				$setData['option_4'] = $request->input('option_4');
				$setData['option_5'] = $request->input('option_5');
				$setData['option_6'] = $request->input('option_6');
				$setData['content_heading'] = $request->input('content_heading');
				$setData['content_description'] = $request->input('content_description');
				$setData['correct_answer'] = 'Test value';
				$setData['status'] = 1;
				
				$record = self::$Questions->CreateRecord($setData);
                echo json_encode(array('heading'=>'Success','msg'=>'Question added successfully'));die;

			}
		}
		return view('/admin/questions/add-page');
    }
	#edit
    public function editPage(Request $request, $row_id){
		$permission = 'edit';
		if(!$this->checkPermission($request->session()->get('admin_id'),10,'edit')){$permission = 'view';}
		$RowID =  base64_decode($row_id);
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}

        if($request->input()){
			$validator = Validator::make($request->all(), [
                'category' => 'required',
				'question' => 'required',
            ],[
                'category.required' => 'Please enter category.',
				'question.required' => 'Please enter question.',
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('category')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('category')));die;
				}
				if($errors->first('question')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('question')));die;
				}
			}else{
				$setData['id'] =  $RowID;
				$setData['category'] = $request->input('category');
				$setData['question'] = $request->input('question');
				$setData['type'] = $request->input('type');
				$setData['option_1'] = $request->input('option_1');
				$setData['option_2'] = $request->input('option_2');
				$setData['option_3'] = $request->input('option_3');
				$setData['option_4'] = $request->input('option_4');
				$setData['option_5'] = $request->input('option_5');
				$setData['option_6'] = $request->input('option_6');
				$setData['content_heading'] = $request->input('content_heading');
				$setData['content_description'] = $request->input('content_description');
				$setData['status'] = $request->input('status');
				self::$Questions->UpdateRecord($setData);
               }
                echo json_encode(array('heading'=>'Success','msg'=>'Question updated successfully'));die;
			}
			$rowData = self::$Questions->where(array('id' => $RowID))->first();
			if(isset($rowData->id)){
				return view('/admin/questions/edit-page',compact('rowData','row_id','permission'));
			}else{
				return redirect('/admin/questions');
			}
    }

}
