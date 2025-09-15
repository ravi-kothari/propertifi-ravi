<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Tiers;
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

class TiersController extends Controller
{
	private static $Tiers;
    private static $TokenHelper;
	public function __construct(){
		self::$Tiers = new Tiers();
        self::$TokenHelper = new TokenHelper();
	}

    #admin dashboard page
    public function getList(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),9,'view')){return redirect('/admin/dashboard/');}
        return view('/admin/tires/index');
    }
    public function listPaginate(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        $query = self::$Tiers->where('status', '!=', 3);
		if($request->input('title')  && $request->input('title') != ""){
            $SearchKeyword = $request->input('title');
            $query->where('title', 'like', '%'.$SearchKeyword.'%');
		}
		if($request->input('status')  && $request->input('status') != ""){
            $query->where('status', $request->input('status'));
		}
		$records =  $query->orderBy('id', 'ASC')->latest()->paginate(100);
        return view('/admin/tires/paginate',compact('records'));
    }
	#add new
    public function addPage(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),9,'addd')){return redirect('/admin/dashboard/');}
		if($request->input()){
			$validator = Validator::make($request->all(), [
                'title' => 'required',
				'price' => 'required|numeric',
				'timings' => 'required|numeric',
            ],[
                'title.required' => 'Please enter title.',
				'price.required' => 'Please enter price.',
				'timings.required' => 'Please enter title.',
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('title')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('title')));die;
				}
				if($errors->first('price')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('price')));die;
				}
				if($errors->first('timings')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('timings')));die;
				}
			}else{

				$setData['title'] = $request->input('title');
				$setData['price'] = $request->input('price');
				$setData['timings'] = $request->input('timings');
				$setData['status'] = 1;
				
				$record = self::$Tiers->CreateRecord($setData);
                echo json_encode(array('heading'=>'Success','msg'=>'Tier added successfully'));die;

			}
		}
		return view('/admin/tires/add-page');
    }
	#edit
    public function editPage(Request $request, $row_id){
		$permission = 'edit';
		if(!$this->checkPermission($request->session()->get('admin_id'),9,'edit')){$permission = 'view';}
		$RowID =  base64_decode($row_id);
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}

        if($request->input()){
			$validator = Validator::make($request->all(), [
                'title' => 'required',
				'price' => 'required|numeric',
				'timings' => 'required|numeric',
            ],[
                'title.required' => 'Please enter title.',
				'price.required' => 'Please enter price.',
				'timings.required' => 'Please enter title.',
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('title')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('title')));die;
				}
				if($errors->first('price')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('price')));die;
				}
				if($errors->first('timings')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('timings')));die;
				}
			}else{
					$setData['id'] =  $RowID;
					$setData['title'] = $request->input('title');
					$setData['price'] = $request->input('price');
					$setData['timings'] = $request->input('timings');
					$setData['status'] = $request->input('status');
                    self::$Tiers->UpdateRecord($setData);
               }
                echo json_encode(array('heading'=>'Success','msg'=>'Tier updated successfully'));die;
			}
		$rowData = self::$Tiers->where(array('id' => $RowID))->first();
        if(isset($rowData->id)){
            return view('/admin/tires/edit-page',compact('rowData','row_id','permission'));
        }else{
            return redirect('/admin/tires');
        }
    }

}
