<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Testimonials;
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

class TestimonialsController extends Controller
{
	private static $Testimonials;
    private static $TokenHelper;
	public function __construct(){
		self::$Testimonials = new Testimonials();
        self::$TokenHelper = new TokenHelper();
	}

    #admin dashboard page
    public function getList(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),13,'view')){return redirect('/admin/dashboard/');}
        return view('/admin/testimonials/index');
    }
    public function listPaginate(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        $query = self::$Testimonials->where('status', '!=', 3);
		if($request->input('title')  && $request->input('title') != ""){
            $SearchKeyword = $request->input('title');
            $query->where('heading', 'like', '%'.$SearchKeyword.'%');
		}
		if($request->input('status')  && $request->input('status') != ""){
            $query->where('status', $request->input('status'));
		}
		$records =  $query->orderBy('id', 'DESC')->latest()->paginate(100);
        return view('/admin/testimonials/paginate',compact('records'));
    }
	#add new
    public function addPage(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),13,'addd')){return redirect('/admin/dashboard/');}
		if($request->input()){
			$validator = Validator::make($request->all(), [
                'heading' => 'required',
            ],[
                'heading.required' => 'Please enter heading.',
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('heading')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('heading')));die;
				}
			}else{
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
						$destination = base_path().'/public/img/testimonials/';
						$request->file->move($destination, $actual_image_name);
						$setData['banner'] = $actual_image_name;
					}
				}
				
				$setData['heading'] = $request->input('heading');
				$setData['description'] = $request->input('description');
				$setData['designation'] = $request->input('designation');
				$setData['status'] = 1;
				
				$record = self::$Testimonials->CreateRecord($setData);
                echo json_encode(array('heading'=>'Success','msg'=>'Testimonial added successfully'));die;

			}
		}
		return view('/admin/testimonials/add-page');
    }
	#edit
    public function editPage(Request $request, $row_id){
		$RowID =  base64_decode($row_id);
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		$permission = 'edit';
		if(!$this->checkPermission($request->session()->get('admin_id'),13,'edit')){$permission = 'view';}

        if($request->input()){
			$validator = Validator::make($request->all(), [
                'heading' => 'required',
            ],[
                'heading.required' => 'Please enter heading.',
            ]);
			if($validator->fails()){
				$errors = $validator->errors();
				if($errors->first('heading')){
                    return json_encode(array('heading'=>'Error','msg'=>$errors->first('heading')));die;
				}
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
                            $destination = base_path().'/public/img/testimonials/';
                            $request->file->move($destination, $actual_image_name);
                            $setData['banner'] = $actual_image_name;

                            if($request->input('old_file') != ""){
                                if(file_exists($destination.$request->input('old_file'))){
                                    unlink($destination.$request->input('old_file'));
                                }
                            }

                        }
                    }
					
					$setData['id'] =  $RowID;
					$setData['heading'] = $request->input('heading');
					$setData['description'] = $request->input('description');
					$setData['designation'] = $request->input('designation');
					$setData['status'] = $request->input('status');
					
					
                    self::$Testimonials->UpdateRecord($setData);
                }
                echo json_encode(array('heading'=>'Success','msg'=>'Testimonial updated successfully'));die;
			}
		$rowData = self::$Testimonials->where(array('id' => $RowID))->first();
        if(isset($rowData->id)){
            return view('/admin/testimonials/edit-page',compact('rowData','row_id','permission'));
        }else{
            return redirect('/admin/testimonials');
        }
    }

}
