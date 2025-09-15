<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AgentRoles;
use App\Models\AgentManagers;
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

class AgentRolesController extends Controller
{
	private static $AgentRoles;
	private static $AgentManagers;
    private static $TokenHelper;
	public function __construct(){
		self::$AgentRoles = new AgentRoles();
		self::$AgentManagers = new AgentManagers();
        self::$TokenHelper = new TokenHelper();
	}

    #admin dashboard page
    public function getList(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        return view('/admin/agent_roles/index');
    }
    public function listPaginate(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        $query = self::$AgentRoles->where('status', '!=', 3)->where('user_id', $request->session()->get('admin_id'));
		if($request->input('title')  && $request->input('title') != ""){
            $SearchKeyword = $request->input('title');
            $query->where('title', 'like', '%'.$SearchKeyword.'%');
		}
		if($request->input('status')  && $request->input('status') != ""){
            $query->where('status', $request->input('status'));
		}
		$records =  $query->orderBy('id', 'DESC')->latest()->paginate(100);
        return view('/admin/agent_roles/paginate',compact('records'));
    }
	#add new
    public function addPage(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
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

				$setData['title'] = $request->input('title');
				$setData['user_id'] = $request->session()->get('admin_id');
				$setData['status'] = 1;
				$setData['addd'] = $request->add && count($request->add) > 0 ? implode(',',$request->add) : NULL;
				$setData['edit'] = $request->edit && count($request->edit) > 0 ? implode(',',$request->edit) : NULL;
				$setData['deletee'] = $request->delete && count($request->delete) > 0 ? implode(',',$request->delete) : NULL;
				$setData['view'] = $request->view && count($request->view) > 0 ? implode(',',$request->view) : NULL;
				
				$record = self::$AgentRoles->CreateRecord($setData);
                echo json_encode(array('heading'=>'Success','msg'=>'User role added successfully'));die;

			}
		}
		$managers = self::$AgentManagers->where(array('status' => 1))->orderBy('title','ASC')->get();
		return view('/admin/agent_roles/add-page',compact('managers'));
    }
	#edit
    public function editPage(Request $request, $row_id){
		$RowID =  base64_decode($row_id);
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}

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
				//print_r($_POST); die;
				$setData['id'] =  $RowID;
				$setData['title'] = $request->input('title');
				$setData['user_id'] = $request->session()->get('admin_id');
				$setData['addd'] = $request->add && count($request->add) > 0 ? implode(',',$request->add) : NULL;
				$setData['edit'] = $request->edit && count($request->edit) > 0 ? implode(',',$request->edit) : NULL;
				$setData['deletee'] = $request->delete && count($request->delete) > 0 ? implode(',',$request->delete) : NULL;
				$setData['view'] = $request->view && count($request->view) > 0 ? implode(',',$request->view) : NULL;

				self::$AgentRoles->UpdateRecord($setData);
			}
			echo json_encode(array('heading'=>'Success','msg'=>'User role updated successfully'));die;
			}
		$rowData = self::$AgentRoles->where(array('id' => $RowID))->first();
		$managers = self::$AgentManagers->where(array('status' => 1))->orderBy('title','ASC')->get();
        if(isset($rowData->id)){
            return view('/admin/agent_roles/edit-page',compact('rowData','row_id','managers'));
        }else{
            return redirect('/admin/agent-roles');
        }
    }

}
