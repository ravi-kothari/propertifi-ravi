<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Blogs;
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

class BlogsController extends Controller
{
	private static $Blogs;
	private static $BlogCategory;
    private static $TokenHelper;
	public function __construct(){
		self::$Blogs = new Blogs();
		self::$BlogCategory = new BlogCategory();
        self::$TokenHelper = new TokenHelper();
	}

    #admin dashboard page
    public function getList(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),6,'view')){return redirect('/admin/dashboard/');}
		$blogCategories = self::$BlogCategory->where('status',1)->orderBy('id','DESC')->get();
        return view('/admin/blogs/index',compact('blogCategories'));
    }
    public function listPaginate(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
        $query = self::$Blogs->where('status', '!=', 3);
		if($request->input('title')  && $request->input('title') != ""){
            $SearchKeyword = $request->input('title');
            $query->where('heading', 'like', '%'.$SearchKeyword.'%');
		}
		if($request->input('blog_category_id')  && $request->input('blog_category_id') != ""){
            $query->where('blog_category_id', $request->input('blog_category_id'));
		}
		if($request->input('status')  && $request->input('status') != ""){
            $query->where('status', $request->input('status'));
		}
		$records =  $query->orderBy('id', 'DESC')->latest()->paginate(100);
		foreach($records as $key => $record){
			$blogCategory = self::$BlogCategory->where('id',$record->blog_category_id)->first();
			$record->cate_name = isset($blogCategory->id) ? $blogCategory->title : 'N/A';
		}
        return view('/admin/blogs/paginate',compact('records'));
    }
	#add new
    public function addPage(Request $request){
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		if(!$this->checkPermission($request->session()->get('admin_id'),6,'addd')){return redirect('/admin/dashboard/');}
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
				$Exist = self::$Blogs->where('heading',$request->input('heading'))->count();
				if($Exist > 0){
					return json_encode(array('heading'=>'Error','msg'=>'Blog heading already exists.'));die;
				}
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
						$destination = base_path().'/public/img/blogs/';
						$request->file->move($destination, $actual_image_name);
						$setData['banner'] = $actual_image_name;
					}
				}
				
				$setData['blog_category_id'] = $request->input('blog_category_id');
				$setData['heading'] = $request->input('heading');
				$setData['slug'] = $this->builtSlug($request->input('heading'));
				$setData['post_date'] = $request->input('post_date');
				$setData['featured'] = $request->input('featured');
				$setData['description'] = $request->input('description');
				$setData['seo_title'] = $request->input('seo_title');
				$setData['seo_keywords'] = $request->input('seo_keywords');
				$setData['seo_description'] = $request->input('seo_description');
				$setData['status'] = 1;
				
				$record = self::$Blogs->CreateRecord($setData);
				
                echo json_encode(array('heading'=>'Success','msg'=>'Blog added successfully'));die;

			}
		}
		$blogCategories = self::$BlogCategory->where('status',1)->orderBy('id','DESC')->get();
		return view('/admin/blogs/add-page',['blogCategories'=>$blogCategories]);
    }
	#edit
    public function editPage(Request $request, $row_id){
		$RowID =  base64_decode($row_id);
		if(!$request->session()->has('admin_email')){return redirect('/admin/');}
		
		$permission = 'edit';
		if(!$this->checkPermission($request->session()->get('admin_id'),6,'edit')){$permission = 'view';}

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
				$Exist = self::$Blogs->where('heading',$request->input('heading'))->where('id','!=',$RowID)->count();
				if($Exist > 0){
					return json_encode(array('heading'=>'Error','msg'=>'Blog heading already exists.'));die;
				}
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
                            $destination = base_path().'/public/img/blogs/';
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
					$setData['blog_category_id'] = $request->input('blog_category_id');
					$setData['heading'] = $request->input('heading');
					//$setData['slug'] = $this->builtSlug($request->input('heading'));
					$setData['post_date'] = $request->input('post_date');
					$setData['featured'] = $request->input('featured');
					$setData['description'] = $request->input('description');
					$setData['seo_title'] = $request->input('seo_title');
					$setData['seo_keywords'] = $request->input('seo_keywords');
					$setData['seo_description'] = $request->input('seo_description');
					$setData['status'] = $request->input('status');
					$setData['schema_tag'] = $request->input('schema_tag');
					$setData['canonical_tag'] = $request->input('canonical_tag');
					
					
                    self::$Blogs->UpdateRecord($setData);
                }
                echo json_encode(array('heading'=>'Success','msg'=>'Blog updated successfully'));die;
			}
		$rowData = self::$Blogs->where(array('id' => $RowID))->first();
        if(isset($rowData->id)){
			$blogCategories = self::$BlogCategory->where('status',1)->orderBy('id','DESC')->get();
            return view('/admin/blogs/edit-page',compact('rowData','row_id','permission','blogCategories'));
        }else{
            return redirect('/admin/blogs');
        }
    }

}
