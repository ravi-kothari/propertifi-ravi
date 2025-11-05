<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Blogs;
use App\Models\Faqs;
use App\Models\InnerPages;
use App\Models\Testimonials;
use App\Models\UserPreferences;
use App\Models\State;
use App\Models\Cities;
use App\Models\User;
use App\Models\Contacts;
use App\Models\Questions;
use App\RouteHelper;
use App\Models\TokenHelper;
use App\Models\Responses;
use App\Models\Lead;
use App\Models\Newsletters;
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
use App\Mail\LeadSubmitMail;
use App\Models\BlogCategory;

class HomePageController extends Controller
{
	private static $Blogs;
	private static $Testimonials;
    private static $TokenHelper;
	private static $Leads;
	private static $Newsletters;
	private static $State;
	private static $Cities;
	private static $Faqs;
	private static $InnerPages;
	private static $UserPreferences;
	private static $User;
	private static $Contacts;
	private static $Questions;
	private static $BlogCategory;
	
	public function __construct(){
		self::$Blogs = new Blogs();
        self::$TokenHelper = new TokenHelper();
		self::$Leads = new Lead();
		self::$Newsletters = new Newsletters();
		self::$Testimonials = new Testimonials();
		self::$State = new State();
		self::$Cities = new Cities();
		self::$Faqs = new Faqs();
		self::$InnerPages = new InnerPages();
		self::$UserPreferences = new UserPreferences();
		self::$User = new User();
		self::$Contacts = new Contacts();
		self::$Questions = new Questions();
		self::$BlogCategory = new BlogCategory();
	}

    #leadSave
    public function leadSave(Request $request){

		$validator = Validator::make($request->all(), [
			'email_address' => 'required|email',
			'full_name' => 'required',
			'property_address' => 'required',
			'property_category' => 'required|numeric',
			'property_city' => 'required',
			'property_number' => 'required|numeric',
			'property_zip' => 'required|numeric',
		],[
			'email_address.required' => 'Please enter email address.',
			'full_name.required' => 'Please enter name.',
			'property_address.required' => 'Please enter address.',
			'property_category.required' => 'Please select category.',
			'property_city.required' => 'Please enter city.',
			'property_number.required' => 'Please enter phone number3.',
			'property_zip.required' => 'Please enter zipcode.',
		]);
		if($validator->fails()){
			$errors = $validator->errors();
			if($errors->first('email_address')){
				return response()->json(['success'=>false, 'message' => $errors->first('email_address')]);
			}
			if($errors->first('full_name')){
				return response()->json(['success'=>false, 'message' => $errors->first('full_name')]);
			}
			if($errors->first('property_address')){
				return response()->json(['success'=>false, 'message' => $errors->first('property_address')]);
			}
			if($errors->first('property_category')){
				return response()->json(['success'=>false, 'message' => $errors->first('property_category')]);
			}
			if($errors->first('property_city')){
				return response()->json(['success'=>false, 'message' => $errors->first('property_city')]);
			}
			if($errors->first('property_number')){
				return response()->json(['success'=>false, 'message' => $errors->first('property_number')]);
			}
			if($errors->first('property_zip')){
				return response()->json(['success'=>false, 'message' => $errors->first('property_zip')]);
			}
		}else{
			$setData['name'] = $request->full_name;
			$setData['email'] = $request->email_address;
			$setData['address'] = $request->property_address;
			$setData['city'] = $request->property_city;
			$setData['zipcode'] = $request->property_zip;
			$setData['category'] = (int)$request->property_category;
			$setData['phone'] = $request->property_number;
			$setData['price_range'] = $request->property_price;
			$setData['questions'] = $request->exam ? json_encode($request->exam) : NULL;
			$setData['price'] = 0;
			$setData['status'] = 1;
			$result = self::$Leads->CreateRecord($setData);
			
			$uniqueID = 'L'.str_pad($result->id, 3, "0", STR_PAD_LEFT);
			self::$Leads->where('id',$result->id)->update(['unique_id' => $uniqueID]);
			
			if($request->email_address != ''){
				$leadData = self::$Leads->where('id',$result->id)->first();
				$data = [
						'userData' => $leadData,
						'subject' => 'Thank you for reaching out!',
					];
				Mail::to([strtolower(trim($request->email_address))])->bcc([strtolower(env('BCC')),strtolower(env('BCC2')),strtolower(env('BCC3')),strtolower(env('BCC4')),strtolower(env('BCC5'))])->send(new LeadSubmitMail($data));
			}
			
			return response()->json(['success'=>true, 'message'=> 'Lead saved successfully'],200);
		}
    }
	#newsletterSave
    public function newsletterSave(Request $request){

		$validator = Validator::make($request->all(), [
			'email_address' => 'required|email',
		],[
			'email_address.required' => 'Please enter email address.',
		]);
		if($validator->fails()){
			$errors = $validator->errors();
			if($errors->first('email_address')){
				return response()->json(['success'=>false, 'message' => $errors->first('email_address')]);
			}
		}else{
			$count = self::$Newsletters->where('email',$request->email_address)->count();
			if($count == 0){
				$setData['email'] = $request->email_address;
				$setData['status'] = 1;
				$count = self::$Newsletters->CreateRecord($setData);
				return response()->json(['success'=>true, 'message'=> 'Newsletter saved successfully'],200);
			}else{
				return response()->json(['success'=>false, 'message'=> 'Your email address already saved.'],200);
			}
			
			
		}
    }
	#faqList
    public function faqList(Request $request){

		$validator = Validator::make($request->all(), [
			'type' => 'required',
		],[
			'type.required' => 'Please enter type.',
		]);
		if($validator->fails()){
			$errors = $validator->errors();
			if($errors->first('type')){
				return response()->json(['success'=>false, 'message' => $errors->first('type')]);
			}
		}else{
			$faqs = self::$Faqs->where('status',1)->get();
			return response()->json(['success'=>true, 'faqs'=> $faqs],200);
		}
    }
	#questionList
    public function questionList(Request $request){

		$validator = Validator::make($request->all(), [
			'type' => 'required',
		],[
			'type.required' => 'Please enter type.',
		]);
		if($validator->fails()){
			$errors = $validator->errors();
			if($errors->first('type')){
				return response()->json(['success'=>false, 'message' => $errors->first('type')]);
			}
		}else{
			$questions = self::$Questions->where('status',1)->where('category',$request->type)->orderBy('id','ASC')->get();
			return response()->json(['success'=>true, 'questions'=> $questions],200);
		}
    }
	#blogCategoryList
    public function blogCategoryList(Request $request){
		$blogCategories = self::$BlogCategory->where('status',1)->orderBy('id','DESC')->get();
		return response()->json(['success'=>true, 'blogCategories'=> $blogCategories],200);
    }
	#blogList
    public function blogList(Request $request){

		$validator = Validator::make($request->all(), [
			'type' => 'required',
		],[
			'type.required' => 'Please enter type.',
		]);
		if($validator->fails()){
			$errors = $validator->errors();
			if($errors->first('type')){
				return response()->json(['success'=>false, 'message' => $errors->first('type')]);
			}
		}else{
			if($request->type == 'Featured'){
				$blogs = self::$Blogs->where('status',1)->orderBy('id','DESC')->get()->take(3);
			}else{
				
				$query = self::$Blogs->where('status',1);
				if($request->input('blog_cate_id')  && $request->input('blog_cate_id') != ""){
					$query->where('blog_category_id',$request->blog_cate_id);
				}
				if($request->input('search_key')  && $request->input('search_key') != ""){
					$query->where('heading', 'like', '%'.$request->input('search_key').'%');
				}
				$blogs = $query->orderBy('id','DESC')->get();
			}
			foreach($blogs as $key => $blog){
				$blogBanner = 'blog-no-img.jpg';
				if($blog->banner != ""){
					$blogBanner = $blog->banner;
				}
				$blog->blog_banner = env('APP_URL').'public/img/blogs/'.$blogBanner;
				$blog->description = strlen($blog->description > 140) ? substr(strip_tags($blog->description),0,140).'...' : strip_tags($blog->description);
				$blog->read_more = strlen($blog->description > 140) ? true : false;
				$blog->post_date = $blog->post_date != "" ? date('d F Y',strtotime($blog->post_date)) : date('d F Y',strtotime($blog->created_at));
			}
			return response()->json(['success'=>true, 'blogs'=> $blogs],200);
		}
    }
	#blogGet
    public function blogGet(Request $request){

		$validator = Validator::make($request->all(), [
			'slug' => 'required',
		],[
			'type.required' => 'Please enter slug.',
		]);
		if($validator->fails()){
			$errors = $validator->errors();
			if($errors->first('slug')){
				return response()->json(['success'=>false, 'message' => $errors->first('slug')]);
			}
		}else{
			$blog = self::$Blogs->where('slug',$request->slug)->where('status',1)->first();
			$blogBanner = 'blog-no-img.jpg';
			if($blog->banner != ""){
				$blogBanner = $blog->banner;
			}
			$blog->banner = env('APP_URL').'public/img/blogs/'.$blogBanner;
			return response()->json(['success'=>true, 'blog'=> $blog],200);
		}
    }
	#testimonialList
    public function testimonialList(Request $request){

		$validator = Validator::make($request->all(), [
			'type' => 'required',
		],[
			'type.required' => 'Please enter type.',
		]);
		if($validator->fails()){
			$errors = $validator->errors();
			if($errors->first('type')){
				return response()->json(['success'=>false, 'message' => $errors->first('type')]);
			}
		}else{
			$testimonials = self::$Testimonials->where('status',1)->get();
			foreach($testimonials as $key => $testimonial){
				$blogBanner = 'blog-no-img.jpg';
				if($testimonial->banner != ""){
					$blogBanner = $testimonial->banner;
				}
				$testimonial->user_banner = env('APP_URL').'public/img/testimonials/'.$blogBanner;
				$testimonial->description = strlen($testimonial->description > 120) ? substr($testimonial->description,0,120).'...' : $testimonial->description;
				$testimonial->read_more = strlen($testimonial->description > 120) ? true : false;
			}
			return response()->json(['success'=>true, 'testimonials'=> $testimonials],200);
		}
    }
	#getTestimonial
    public function getTestimonial(Request $request){

		$validator = Validator::make($request->all(), [
			'id' => 'required',
		],[
			'id.required' => 'Please enter id.',
		]);
		if($validator->fails()){
			$errors = $validator->errors();
			if($errors->first('id')){
				return response()->json(['success'=>false, 'message' => $errors->first('id')]);
			}
		}else{
			$testimonial = self::$Testimonials->where('id',$request->id)->first();
			$blogBanner = 'blog-no-img.jpg';
			if($testimonial->banner != ""){
				$blogBanner = $testimonial->banner;
			}
			$testimonial->user_banner = env('APP_URL').'public/img/testimonials/'.$blogBanner;
			
			$nextID = 0;
			$testimonialNext = self::$Testimonials->where('id','>',$request->id)->orderBy('id', 'asc')->limit(1)->first();
			if(isset($testimonialNext->id)){
				$nextID = $testimonialNext->id;
			}
			$previousID = 0;
			$testimonialPre = self::$Testimonials->where('id','<',$request->id)->orderBy('id', 'desc')->limit(1)->first();
			if(isset($testimonialPre->id)){
				$previousID = $testimonialPre->id;
			}
			
			$testimonial->next_id = $nextID;
			$testimonial->previous_id = $previousID;
			
			return response()->json(['success'=>true, 'testimonial'=> $testimonial],200);
		}
    }
	#stateCodeList
    public function stateCodeList(Request $request){
		
		$length = $request->length;
		if($length == 'Full'){
			$states = self::$State->where('country_id',231)->where('status',1)->orderBy('state','ASC')->get();
		}else{
			$states = self::$State->where('country_id',231)->where('status',1)->orderBy('state','ASC')->get()->take(6);
		}
		
		$stateData = self::$State->where('country_id',231)->where('status',1)->orderBy('state','ASC')->first();
		return response()->json(['success'=>true, 'length' => $length, 'states'=> $states,'first_state' => $stateData],200);
    }
	#cityList
    public function cityList(Request $request){

		$validator = Validator::make($request->all(), [
			'state_id' => 'required',
		],[
			'state_id.required' => 'Please enter state_id.',
		]);
		if($validator->fails()){
			$errors = $validator->errors();
			if($errors->first('state_id')){
				return response()->json(['success'=>false, 'message' => $errors->first('state_id')]);
			}
		}else{
			$length = $request->length;
			$cityArray = [];
			$cities = self::$Cities->where('state_id',$request->state_id)->where('status',1)->orderBy('city','ASC')->get();
			
			foreach($cities as $key => $city){
				$userCount = self::$User->where('city',$city->id)->where('status',1)->count();
				if($userCount > 0){
					$cityArray[] = $city;
				}
				if($length == 'Half'){
					if(count($cityArray) == 3){
						break;
					}
				}
			}
			return response()->json(['success'=>true, 'length' => $length, 'cities'=> $cityArray, 'count' => $cities->count()],200);
		}
    }
	#getPageContent
    public function getPageContent(Request $request){

		$validator = Validator::make($request->all(), [
			'page_id' => 'required',
		],[
			'page_id.required' => 'Please enter page_id.',
		]);
		if($validator->fails()){
			$errors = $validator->errors();
			if($errors->first('page_id')){
				return response()->json(['success'=>false, 'message' => $errors->first('page_id')]);
			}
		}else{
			$pageData = self::$InnerPages->where('id',$request->page_id)->where('status',1)->first();
			return response()->json(['success'=>true, 'pageData'=> $pageData],200);
		}
    }
	#getLaw
    public function getLaw(Request $request){
		$validator = Validator::make($request->all(), [
			'state' => 'required',
		],[
			'state.required' => 'Please enter state.'
		]);
		if($validator->fails()){
			$errors = $validator->errors();
			if($errors->first('state')){
				return response()->json(['success'=>false, 'message' => $errors->first('state')]);
			}
		}else{
			$stateData = self::$State->where('slug',$request->state)->first();
			/*$laws = str_replace("Last Updated","LastUpdated",$stateData->laws);
			$laws = str_replace("Landlord Responsibilities","LandlordResponsibilities",$laws);
			$laws = str_replace("Tenant Responsibilities","TenantResponsibilities",$laws);
			$laws = str_replace("Evictions in Alabama","Evictions",$laws);
			$laws = str_replace("Security Deposits","SecurityDeposits",$laws);
			$laws = str_replace("Lease Termination in Alabama","LeaseTermination",$laws);
			$laws = str_replace("Rent Increases in Alabama","RentIncreases",$laws);
			$laws = str_replace("Discrimination in Alabama","Discrimination",$laws);
			$laws = str_replace("Additional Landlord Tenant Regulations","AdditionalLandlord",$laws);
			$laws = str_replace("Landlord Right to Entry in Alabama","LandlordRightEntry",$laws);
			$laws = str_replace("Rent Collection & Fees in Alabama","RentCollection",$laws);
			$laws = str_replace("Small Claims Court in Alabama","SmallClaimsCourt",$laws);
			$laws = str_replace("Mandatory Disclosures in Alabama","MandatoryDisclosures",$laws);
			$laws = str_replace("Changing the Locks in Alabama","ChangingLocks",$laws);
			$laws = str_replace("Local Landlord Tenant Laws in Alabama","LocalLandlordTenant",$laws);
			
			self::$State->where('slug',$request->state)->update(['laws' => $laws]);
			$stateData = self::$State->where('slug',$request->state)->first();*/
			return response()->json(['success'=>true, 'stateData'=> $stateData],200);
		}
	}
	#getAgentList
    public function getAgentList(Request $request){
		
		$validator = Validator::make($request->all(), [
			'cate_id' => 'required',
			'state' => 'required',
			'city' => 'required',
		],[
			'cate_id.required' => 'Please enter cate_id.',
			'state.required' => 'Please enter state.',
			'city.required' => 'Please enter city.',
		]);
		if($validator->fails()){
			$errors = $validator->errors();
			if($errors->first('cate_id')){
				return response()->json(['success'=>false, 'message' => $errors->first('cate_id')]);
			}
			if($errors->first('state')){
				return response()->json(['success'=>false, 'message' => $errors->first('state')]);
			}
			if($errors->first('city')){
				return response()->json(['success'=>false, 'message' => $errors->first('city')]);
			}
		}else{
		
			$stateData = self::$State->where('country_id',231)->where('abbreviation_slug',$request->state)->first();
			if(!isset($stateData->id)){
				return response()->json(['success'=>false, 'message' => 'Invalid state', 'agents'=> []],200);
			}
			$cityData = self::$Cities->where('state_id',$stateData->id)->where('slug',$request->city)->first();
			if(!isset($cityData->id)){
				return response()->json(['success'=>false, 'stateData'=> $stateData->id, 'message' => 'Invalid city', 'agents'=> []],200);
			}
			
			//$agents = self::$UserPreferences->join('users', 'users.id', '=', 'user_preferences.user_id')->select('user_preferences.*','users.company_name','users.address','users.city','users.state','users.about','users.photo','users.featured')->where('users.state',$stateData->id)->where('users.city',$cityData->id)->where('user_preferences.pricing_id',$request->cate_id)->where('user_preferences.status',1)->paginate(9);
			
			$agents = self::$User->select('users.id as user_id','users.company_name','users.address','users.city','users.state','users.about','users.photo','users.featured','users.slug')->where('users.state',$stateData->id)->where('users.city',$cityData->id)->where('users.status',1)->paginate(120);
			
			foreach($agents as $key => $agent){
				$agent->company_name = strlen($agent->company_name) > 20 ? substr($agent->company_name,0,20).'...' : $agent->company_name;
				$agent->about = strlen($agent->about) > 120 ? substr($agent->about,0,120).'...' : $agent->about;
				$agent->read_more = strlen($agent->about) > 120 ? true : false;
				$agent->featured = $agent->featured==1 ? true : false;
				$logo = 'company-no-img.jpg';
				if($agent->photo != ""){
					$logo = $agent->photo;
				}
				$agent->photo = env('APP_URL').'public/img/users/'.$logo;
				
				if($request->cate_id == 1){
					$agent->category = 'Single Family';
				}
				if($request->cate_id == 2){
					$agent->category = 'Multi-Family';
				}
				if($request->cate_id == 3){
					$agent->category = 'Association';
				}
				if($request->cate_id == 8){
					$agent->category = 'Commercial';
				}
			}
			return response()->json(['success'=>true,'stateData'=> $stateData->id,'cityData'=> $cityData->id,'agents'=> $agents,'cityName'=> $cityData->city,'stateName'=> $stateData->state,'cityRecord' => $cityData],200);
		}
		
		
    }
	#getAgent
    public function getAgent(Request $request){
		
		$validator = Validator::make($request->all(), [
			'agent_id' => 'required',
			'state' => 'required',
			'city' => 'required',
		],[
			'agent_id.required' => 'Please enter agent_id.',
			'state.required' => 'Please enter state.',
			'city.required' => 'Please enter city.',
		]);
		if($validator->fails()){
			$errors = $validator->errors();
			if($errors->first('agent_id')){
				return response()->json(['success'=>false, 'message' => $errors->first('agent_id')]);
			}
			if($errors->first('state')){
				return response()->json(['success'=>false, 'message' => $errors->first('state')]);
			}
			if($errors->first('city')){
				return response()->json(['success'=>false, 'message' => $errors->first('city')]);
			}
		}else{
			
			$stateData = self::$State->where('country_id',231)->where('abbreviation_slug',$request->state)->first();
			if(!isset($stateData->id)){
				return response()->json(['success'=>false, 'agent_data'=> []],200);
			}
			$cityData = self::$Cities->where('state_id',$stateData->id)->where('slug',$request->city)->first();
			if(!isset($cityData->id)){
				return response()->json(['success'=>false, 'agent_data'=> []],200);
			}
		
			$agentData = self::$User->where('slug',$request->agent_id)->where('state',$stateData->id)->where('city',$cityData->id)->first();
			
			if(!isset($agentData->company_name)){
				return response()->json(['success'=>false, 'agent_data'=> []],200);
			}
			
			$logo = 'company-no-img.jpg';
			if($agentData->photo != ""){
				$logo = $agentData->photo;
			}
			$agentData->photo = env('APP_URL').'public/img/users/'.$logo;
			
			$agentData->featured = $agentData->featured==1 ? true : false;
			
			return response()->json(['success'=>true, 'cityName'=> $cityData->city,'stateName'=> $stateData->state,'agent_data'=> $agentData],200);
		}
		
		
    }
	#contactSave
    public function contactSave(Request $request){
		
		$validator = Validator::make($request->all(), [
			'name' => 'required|alpha_num:ascii',
			'email' => 'required|email',
			'mobile' => 'required|numeric',
			'message' => 'required',
		],[
			'name.required' => 'Please enter name.',
			'email.required' => 'Please enter email.',
			'mobile.required' => 'Please enter mobile.',
			'message.required' => 'Please enter message.',
		]);
		if($validator->fails()){
			$errors = $validator->errors();
			if($errors->first('name')){
				return response()->json(['success'=>false, 'message' => $errors->first('name')]);
			}
			if($errors->first('email')){
				return response()->json(['success'=>false, 'message' => $errors->first('email')]);
			}
			if($errors->first('mobile')){
				return response()->json(['success'=>false, 'message' => $errors->first('mobile')]);
			}
			if($errors->first('message')){
				return response()->json(['success'=>false, 'message' => $errors->first('message')]);
			}
		}else{
			if(strlen($request->mobile) != 10){
				return response()->json(['success'=>false, 'message'=> 'Please enter 10 digit mobile no'],200);
			}
			$setData['name'] = $request->name;
			$setData['email'] = $request->email;
			$setData['contact'] = $request->mobile;
			$setData['message'] = $request->message;
			$setData['user_id'] = $request->agent_id;
			self::$Contacts->CreateRecord($setData);
			return response()->json(['success'=>true, 'message'=> 'Message sent successfully'],200);
		}
		
		
    }
	#agentSave
    public function agentSave(Request $request){
		
		$validator = Validator::make($request->all(), [
			'company_name' => 'required',
			'email' => 'required|email',
			'mobile' => 'required|numeric',
		],[
			'company_name.required' => 'Please enter company name.',
			'email.required' => 'Please enter email address.',
			'mobile.required' => 'Please enter mobile number.',
		]);
		if($validator->fails()){
			$errors = $validator->errors();
			if($errors->first('company_name')){
				return response()->json(['success'=>false, 'message' => $errors->first('company_name')]);
			}
			if($errors->first('email')){
				return response()->json(['success'=>false, 'message' => $errors->first('email')]);
			}
			if($errors->first('mobile')){
				return response()->json(['success'=>false, 'message' => $errors->first('mobile')]);
			}
		}else{
			if(strlen($request->mobile) != 10){
				return response()->json(['success'=>false, 'message'=> 'Invalid mobile no'],200);
			}
			if(!self::$User->ExistingRecord($request->input('email'))){
				if(!self::$User->ExistingMobileRecord($request->input('mobile'))){
					$setData['company_name'] = $request->company_name;
					$setData['email'] = $request->email;
					$setData['mobile'] = $request->mobile;
					$setData['country_code'] = $request->country_code;
					$setData['type'] = 'Agent';
					$setData['email_varification'] = 2;
					$setData['status'] = 1;
					$record = self::$User->CreateRecord($setData);
					if($request->email != ''){
						$userData = self::$User->where('id',$record->id)->first();
						$data = [
								'userData' => $userData,
								'subject' => ' Welcome to Propertifi - Complete Your Profile to Start Receiving Property Matches',
							];
						Mail::to([strtolower(trim($request->email))])->bcc([strtolower(env('BCC'))])->send(new SignupMail($data));
					}
					return response()->json(['success'=>true, 'message'=> 'Registration successfully'],200);
				}else{
					return response()->json(['success'=>false, 'message'=> 'Phone no already exist'],200);
				}
				
			}else{
				return response()->json(['success'=>false, 'message'=> 'Email address already exist'],200);
			}
			
		}
		
		
    }
	
}
