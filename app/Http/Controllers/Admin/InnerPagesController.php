<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\InnerPages;
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

class InnerPagesController extends Controller
{
	private static $InnerPages;
    private static $TokenHelper;
	public function __construct(){
		self::$InnerPages = new InnerPages();
        self::$TokenHelper = new TokenHelper();
	}

    #admin dashboard page
    public function getList(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),8,'view')){return redirect('/admin/dashboard/');}
        return view('/admin/inner_pages/index');
    }
    public function listPaginate(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        $query = self::$InnerPages->where('status', '!=', 3);
		if($request->input('title')  && $request->input('title') != ""){
            $SearchKeyword = $request->input('title');
            $query->where('heading', 'like', '%'.$SearchKeyword.'%');
		}
		if($request->input('status')  && $request->input('status') != ""){
            $query->where('status', $request->input('status'));
		}
		$records =  $query->orderBy('id', 'DESC')->latest()->paginate(100);
        return view('/admin/inner_pages/paginate',compact('records'));
    }
	#edit
    public function editPage(Request $request, $row_id){
		$permission = 'edit';
		if(!$this->checkPermission($request->session()->get('admin_id'),8,'edit')){$permission = 'view';}
		$RowID =  base64_decode($row_id);
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}

        if($request->input()){
			$validator = Validator::make($request->all(), [
                'page_name' => 'required',
            ],[
                'page_name.required' => 'Please enter page name.',
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('heading')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('heading')));die;
				}
			}else{
					$setData['id'] =  $RowID;
					$setData['page_name'] = $request->input('page_name');
					$setData['heading'] = $request->input('heading');
					$setData['seo_title'] = $request->input('seo_title');
					$setData['seo_keywords'] = $request->input('seo_keywords');
					$setData['seo_description'] = $request->input('seo_description');
					$setData['description'] = $request->input('description');
					$setData['short_description'] = $request->input('short_description');
					$setData['status'] = 1;
                    self::$InnerPages->UpdateRecord($setData);
                }
                echo json_encode(array('heading'=>'Success','msg'=>'Page updated successfully'));die;
			}
		$rowData = self::$InnerPages->where(array('id' => $RowID))->first();
        if(isset($rowData->id)){
            return view('/admin/inner_pages/edit-page',compact('rowData','row_id','permission'));
        }else{
            return redirect('/admin/inner-pages');
        }
    }

}
