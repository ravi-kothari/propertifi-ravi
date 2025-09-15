<?php 
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LeadDistribute extends Command
{
    protected $signature = 'LeadDistribute:check';
    protected $description = 'This laravel cronjob is used to distribute leads';

    public function __construct()
    {
        parent::__construct();
        
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
		Log:info('File Details: Hello',['time'=>\date('Y-m-d H:i:s')]);
		DB::table('blogs')->where('id',1)->update(['featured' => 1]);
		echo 'Success'; die;
		$leads = DB::table('leads')->get();
		$currentTime = time();
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
								if($userData->credits >= $tierData->price){
									DB::table('user_leads')->insert(['user_id' => $user->user_id,'lead_id' => $lead->id,'status' => 1,'lead_unique_id' => $lead->unique_id,'property_type' => $lead->category,'lead_date' => $lead->created_at,'price' => $tierData->price,'tier_id' => $tierData->id]);
									
									$remainingBalance = $userData->credits-$tierData->price;
									DB::table('users')->where('id',$user->user_id)->update(['credits' => $remainingBalance]);
								}
							}else{
								DB::table('user_leads')->insert(['user_id' => $user->user_id,'lead_id' => $lead->id,'status' => 1,'lead_unique_id' => $lead->unique_id,'property_type' => $lead->category,'lead_date' => $lead->created_at,'price' => $tierData->price,'tier_id' => $tierData->id]);
							}
						}
					}
				}
			}
		}
		//$setdata['status'] = 1;
		//DB::table('blogs')->where('id',1)->update($setdata); 
		echo 'Success'; die;
    }
}