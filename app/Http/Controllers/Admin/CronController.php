<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Contacts;
use App\RouteHelper;
use App\Models\TokenHelper;
use App\Models\Responses;
use App\Models\User;
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
use Illuminate\Support\Facades\Log;
use App\Mail\LeadDistributeMail;

class CronController extends Controller{
	
	private static $Contacts;
    private static $TokenHelper;
	private static $User;
	
	private const SCORE_WEIGHTS = [
        'delivery_speed_preference' => 0.50,
        'location_match'            => 0.25,
        'category_match'            => 0.10,
        'distribution_fairness'     => 0.10,
        'tier_preference'           => 0.05,
    ];
	
	public function __construct(){
		self::$Contacts = new Contacts();
        self::$TokenHelper = new TokenHelper();
		self::$User = new User();
	}
	#sendEmailNotification
    public function sendEmailNotification($userID,$leadID){
		$userDataForEmail = DB::table('users')->select('email','company_name')->where('id',$userID)->first();
		if($userDataForEmail->email != ''){
			$leadData = DB::table('leads')->where('id',$leadID)->first();
			$data = [
			'userData' => (array)$userDataForEmail,
			'leadData' => (array)$leadData,
			'subject' => $userDataForEmail->company_name.', You Have a New Propertifi Match!',
			];
			//Mail::to([strtolower(trim($userDataForEmail->email))])->bcc([strtolower(env('BCC'))])->send(new LeadDistributeMail($data));
		}
		return true;
	}
	private function calculateDeliverySpeedScore(object $user): float
    {
        // A 'timings' value of 0 indicates the user is on a tier that receives leads instantly.
        return ($user->timings == 0) ? 1.0 : 0.0;
    }
	private function calculateLocationMatch(object $lead, $zipcode): float
    {
        if ($lead->zipcode == $zipcode) {
            return 1.0; // Level 1: Perfect match
        }
        return 0.0;
    }
	private function calculateCategoryMatch(object $lead, object $user): float
    {
        return $lead->category == $user->pricing_id ? 1.0 : 0.0;
    }
	/**
     * Calculates a "fairness" score to ensure leads are distributed more evenly.
     */
    private function calculateDistributionFairnessScore(object $user): float
    {
        $lastLeadTime = DB::table('user_leads')
            ->where('user_id', $user->user_id)
            ->latest('created_at')
            ->value('created_at');

        if (is_null($lastLeadTime)) {
            return 1.0;
        }

        $hoursSinceLastLead = now()->diffInHours($lastLeadTime);
        $normalizationPeriodHours = 72.0;
        $score = $hoursSinceLastLead / $normalizationPeriodHours;

        return min($score, 1.0);
    }
	/**
     * Gives a higher score to users on higher-priced (premium) tiers.
     */
    private function calculateTierPreferenceScore($price): float
    {
        $maxTierPrice = 50.0; 
        if ($maxTierPrice == 0) return 0.0;
        $score = $price / $maxTierPrice;
        return min($score, 1.0);
    }
    #admin dashboard page
    public function leadDistribute(Request $request){
		//Log:info('File Details: Hello',['time'=>\date('Y-m-d H:i:s')]);
		$startTime = date('Y-m-d ').'00:00:00';
		$endTime = date('Y-m-d ').'23:59:00';
		$currentTime = time();
		$leads = DB::table('leads')->where('status',1)->whereBetween('created_at', [$startTime, $endTime])->get();
		foreach($leads as $key => $lead){
			$users = DB::table('user_preferences')->join('user_zipcodes', 'user_preferences.user_id', '=', 'user_zipcodes.user_id')->select('user_preferences.*')->where('user_preferences.pricing_id',$lead->category)->where('user_preferences.status',1)->where('user_zipcodes.zipcode',$lead->zipcode)->where('user_zipcodes.status',1)->get();
			foreach($users as $key => $user){
				$sendTime = strtotime(date('Y-m-d H:i:s',strtotime('+'.$user->timings.' minutes', strtotime($lead->created_at))));
				if($currentTime >= $sendTime){
					$leadLinked = DB::table('user_leads')->where('user_id',$user->user_id)->where('lead_id',$lead->id)->where('status',1)->first();
					if(!isset($leadLinked->id)){
						$tierData = DB::table('tiers')->where('id',$user->tier_id)->where('status',1)->first();
						if(isset($tierData->id)){
							if($tierData->price > 0){
								$userData = DB::table('users')->select('credits')->where('id',$user->user_id)->first();
								$totalAmt = $tierData->price;
								if($userData->credits >= $totalAmt){
									
									$userData = DB::table('users')->where('id',$user->user_id)->first();
									
									$delivery_speed_preference = $this->calculateDeliverySpeedScore($user);
									$location_match = $this->calculateLocationMatch($lead,$userData->zipcode);
									$category_match = $this->calculateCategoryMatch($lead,$user);
									$distribution_fairness = $this->calculateDistributionFairnessScore($user);
									$tier_preference = $this->calculateTierPreferenceScore($tierData->price);
									
									$totalScore = $this->calculateTotalScore($delivery_speed_preference,$location_match,$category_match,$distribution_fairness,$tier_preference);
									
									DB::table('user_leads')->insert(['user_id' => $user->user_id,'lead_id' => $lead->id,'status' => 1,'lead_unique_id' => $lead->unique_id,'property_type' => $lead->category,'lead_date' => $lead->created_at,'price' => $totalAmt,'tier_id' => $tierData->id,'delivery_speed_preference' => $delivery_speed_preference,'location_match' => $location_match,'category_match' => $category_match,'distribution_fairness' => $distribution_fairness,'tier_preference' => $tier_preference,'total_score' => $totalScore]);
									
									$remainingBalance = $userData->credits-$totalAmt;
									DB::table('users')->where('id',$user->user_id)->update(['credits' => $remainingBalance]);
									
									$this->sendEmailNotification($user->user_id,$lead->id);
									
								}else{
									$this->saveUserPreference($user->user_id,$lead->category,$tierData->id,$tierData->timings);
								}
							}else{
								$this->saveUserLead($user->user_id,$lead,$tierData->price,$tierData->id,$user);
							}
						}
					}
				}
			}
		}
		foreach($leads as $key => $lead){
			$users = DB::table('users')->where('status',1)->where('zipcode',$lead->zipcode)->get();
			if($users->count() > 0){
				foreach($users as $key => $user){
					$this->sendLeadToUser($lead,$user);
				}
			}else{
				$zipData = DB::table('zipcodes')->where('status',1)->where('zip_code',$lead->zipcode)->first();
				if(isset($zipData->id)){
					$cityZipcodes = DB::table('zipcodes')->where('status',1)->where('city',$zipData->city)->get();
					foreach($cityZipcodes as $key => $cityZipcode){
						$users = DB::table('users')->where('status',1)->where('zipcode',$cityZipcode->zip_code)->get();
						foreach($users as $key => $user){
							$this->sendLeadToUser($lead,$user);
						}
					}
				}
			}
		}
		echo 'Success'; die;
    }
	#sendLeadToUser
	public function sendLeadToUser($lead,$user){
		$currentTime = time();
		$userPreference = DB::table('user_preferences')->where('status',1)->where('pricing_id',$lead->category)->where('user_id',$user->id)->first();
		if(isset($userPreference->id)){
			$sendTime = strtotime(date('Y-m-d H:i:s',strtotime('+'.$userPreference->timings.' minutes', strtotime($lead->created_at))));
			if($currentTime >= $sendTime){
				$leadLinked = DB::table('user_leads')->where('user_id',$user->id)->where('lead_id',$lead->id)->where('status',1)->first();
				if(!isset($leadLinked->id)){
					$tierData = DB::table('tiers')->where('id',$userPreference->tier_id)->where('status',1)->first();
					if($tierData->price > 0){
						$totalAmt = $tierData->price;
						if($user->credits >= $totalAmt){
							
							$delivery_speed_preference = $this->calculateDeliverySpeedScore($userPreference);
							$location_match = $this->calculateLocationMatch($lead,$user->zipcode);
							$category_match = $this->calculateCategoryMatch($lead,$userPreference);
							$distribution_fairness = $this->calculateDistributionFairnessScore($userPreference);
							$tier_preference = $this->calculateTierPreferenceScore($tierData->price);
							
							$totalScore = $this->calculateTotalScore($delivery_speed_preference,$location_match,$category_match,$distribution_fairness,$tier_preference);
							
							DB::table('user_leads')->insert(['user_id' => $user->id,'lead_id' => $lead->id,'status' => 1,'lead_unique_id' => $lead->unique_id,'property_type' => $lead->category,'lead_date' => $lead->created_at,'price' => $totalAmt,'tier_id' => $tierData->id,'delivery_speed_preference' => $delivery_speed_preference,'location_match' => $location_match,'category_match' => $category_match,'distribution_fairness' => $distribution_fairness,'tier_preference' => $tier_preference,'total_score' => $totalScore]);
							
							$remainingBalance = $user->credits-$totalAmt;
							DB::table('users')->where('id',$user->id)->update(['credits' => $remainingBalance]);
							$this->sendEmailNotification($user->id,$lead->id);
						}else{
							$this->saveUserPreference($user->id,$lead->category,$tierData->id,$tierData->timings);
						}
					}else{
						$this->saveUserLead($user->id,$lead,$tierData->price,$tierData->id,$userPreference);
					}
				}
			}
			
		}else{
			$this->saveUserPreference($user->id,$lead->category,$tierData->id,$tierData->timings);
		}
		return true;
	}
	#saveUserPreference
	public function saveUserPreference($userID,$leadCategory,$tierID,$timings){
		$tierData = DB::table('tiers')->where('price',0)->where('status',1)->first();
		if(isset($tierData->id)){
			DB::table('user_preferences')->insert(['user_id' => $userID,'pricing_id' => $leadCategory,'status' => 1,'tier_id' => $tierID,'timings' => $timings]);
		}
		return true;
	}
	#saveUserLead
	public function saveUserLead($userID,$lead,$totalAmt,$tierID,$userPreference){
		
		$userData = DB::table('users')->where('id',$userID)->first();
		$delivery_speed_preference = $this->calculateDeliverySpeedScore($userPreference);
		$location_match = $this->calculateLocationMatch($lead,$userData->zipcode);
		$category_match = $this->calculateCategoryMatch($lead,$userPreference);
		$distribution_fairness = $this->calculateDistributionFairnessScore($userPreference);
		$tier_preference = $this->calculateTierPreferenceScore($totalAmt);
		
		$totalScore = $this->calculateTotalScore($delivery_speed_preference,$location_match,$category_match,$distribution_fairness,$tier_preference);
		
		DB::table('user_leads')->insert(['user_id' => $userID,'lead_id' => $lead->id,'status' => 1,'lead_unique_id' => $lead->unique_id,'property_type' => $lead->category,'lead_date' => $lead->created_at,'price' => $totalAmt,'tier_id' => $tierID,'delivery_speed_preference' => $delivery_speed_preference,'location_match' => $location_match,'category_match' => $category_match,'distribution_fairness' => $distribution_fairness,'tier_preference' => $tier_preference,'total_score' => $totalScore]);
		$this->sendEmailNotification($userID,$lead->id);
		return true;
	}
	#calculateTotalScore
	public function calculateTotalScore($delivery_speed_preference,$location_match,$category_match,$distribution_fairness,$tier_preference){
		$scores = [
            'delivery_speed_preference' => $delivery_speed_preference,
            'location_match'            => $location_match,
            'category_match'            => $category_match,
            'distribution_fairness'     => $distribution_fairness,
            'tier_preference'           => $tier_preference,
        ];

        $totalScore = 0.0;
        foreach ($scores as $metric => $score) {
            if (isset(self::SCORE_WEIGHTS[$metric])) {
                $totalScore += $score * self::SCORE_WEIGHTS[$metric];
            }
        }
		return $totalScore;
	}
}
