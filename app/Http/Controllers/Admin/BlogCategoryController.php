<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
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

class BlogCategoryController extends Controller{
	
	private static $BlogCategory;
    private static $TokenHelper;
	
	public function __construct(){
		self::$BlogCategory = new BlogCategory();
        self::$TokenHelper = new TokenHelper();
	}

    #admin dashboard page
    public function getList(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),6,'view')){return redirect('/admin/dashboard/');}
        return view('/admin/blog_categories/index');
    }
    public function listPaginate(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        $query = self::$BlogCategory->where('status', '!=', 3);
		if($request->input('title')  && $request->input('title') != ""){
            $SearchKeyword = $request->input('title');
            $query->where('title', 'like', '%'.$SearchKeyword.'%');
		}
		if($request->input('status')  && $request->input('status') != ""){
            $query->where('status', $request->input('status'));
		}
		$records =  $query->orderBy('id', 'DESC')->latest()->paginate(100);
        return view('/admin/blog_categories/paginate',compact('records'));
    }
	#add new
    public function addPage(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),6,'addd')){return redirect('/admin/dashboard/');}
		if($request->input()){
			$validator = Validator::make($request->all(), [
                'title' => 'required',
            ],[
                'title.required' => 'Please enter title.',
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('title')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('title')));die;
				}
			}else{
				$Exist = self::$BlogCategory->where('title',$request->input('title'))->count();
				if($Exist > 0){
					return json_encode(array('heading'=>'Error','msg'=>'Blog category already exists.'));die;
				}

				$setData['title'] = $request->input('title');
				$setData['status'] = 1;
				
				$record = self::$BlogCategory->CreateRecord($setData);
                echo json_encode(array('heading'=>'Success','msg'=>'Blog category added successfully'));die;

			}
		}
		return view('/admin/blog_categories/add-page');
    }
	#edit
    public function editPage(Request $request, $row_id){
		$RowID =  base64_decode($row_id);
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		
		$permission = 'edit';
		if(!$this->checkPermission($request->session()->get('admin_id'),6,'edit')){$permission = 'view';}

        if($request->input()){
			$validator = Validator::make($request->all(), [
                'title' => 'required',
            ],[
                'title.required' => 'Please enter title.',
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('title')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('title')));die;
				}
			}else{
				$Exist = self::$BlogCategory->where('title',$request->input('title'))->where('id','!=',$RowID)->count();
				if($Exist > 0){
					return json_encode(array('heading'=>'Error','msg'=>'Blog category already exists.'));die;
				}

					
					$setData['id'] =  $RowID;
					$setData['title'] = $request->input('title');
					$setData['status'] = $request->input('status');
					
					
                    self::$BlogCategory->UpdateRecord($setData);
                }
                echo json_encode(array('heading'=>'Success','msg'=>'Blog category updated successfully'));die;
			}
		$rowData = self::$BlogCategory->where(array('id' => $RowID))->first();
        if(isset($rowData->id)){
            return view('/admin/blog_categories/edit-page',compact('rowData','row_id','permission'));
        }else{
            return redirect('/admin/blog-categories');
        }
    }

}
