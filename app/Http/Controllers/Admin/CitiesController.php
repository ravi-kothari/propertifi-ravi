<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Cities;
use App\Models\State;
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

class CitiesController extends Controller{
	
	private static $Cities;
	private static $State;
    private static $TokenHelper;
	
	public function __construct(){
		self::$Cities = new Cities();
        self::$TokenHelper = new TokenHelper();
		self::$State = new State();
	}

    #admin dashboard page
    public function getList(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),6,'view')){return redirect('/admin/dashboard/');}
        return view('/admin/cities/index');
    }
    public function listPaginate(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        $query = self::$Cities->where('status', '!=', 3)->where('country_id',231);
		if($request->input('title')  && $request->input('title') != ""){
            $SearchKeyword = $request->input('title');
            $query->where('city', 'like', '%'.$SearchKeyword.'%');
		}
		if($request->input('status')  && $request->input('status') != ""){
            $query->where('status', $request->input('status'));
		}
		$records =  $query->orderBy('id', 'DESC')->latest()->paginate(50);
		foreach($records as $key => $record){
			$stateData = self::$State->where(array('id' => $record->state_id))->first();
			$record->state_name = isset($stateData->state) ? $stateData->state : '';
		}
        return view('/admin/cities/paginate',compact('records'));
    }
	#edit
    public function editPage(Request $request, $row_id){
		$RowID =  base64_decode($row_id);
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		
		$permission = 'edit';
		if(!$this->checkPermission($request->session()->get('admin_id'),15,'edit')){$permission = 'view';}

        if($request->input()){
			$validator = Validator::make($request->all(), [
                'city' => 'required',
            ],[
                'city.required' => 'Please enter city.',
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('city')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('city')));die;
				}
			}else{
				/*$Exist = self::$Cities->where('city',$request->input('city'))->where('id','!=',$RowID)->where('status',1)->count();
				if($Exist > 0){
					return json_encode(array('heading'=>'Error','msg'=>'City already exists.'));die;
				}*/

					
					$setData['id'] =  $RowID;
					$setData['city'] = $request->input('city');
					$setData['seo_title'] = $request->input('seo_title');
					$setData['seo_keywords'] = $request->input('seo_keywords');
					$setData['seo_description'] = $request->input('seo_description');
					$setData['status'] = $request->input('status');
					
					
                    self::$Cities->UpdateRecord($setData);
                }
                echo json_encode(array('heading'=>'Success','msg'=>'City updated successfully'));die;
			}
		$rowData = self::$Cities->where(array('id' => $RowID))->first();
        if(isset($rowData->id)){
            return view('/admin/cities/edit-page',compact('rowData','row_id','permission'));
        }else{
            return redirect('/admin/cities');
        }
    }

}
