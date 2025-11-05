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
		Log::info('Lead Distribution Started', ['time' => date('Y-m-d H:i:s')]);

		// Get all leads that need to be distributed
		$leads = DB::table('leads')->get();
		$currentTime = time();
		$distributedCount = 0;

		foreach($leads as $key => $lead){
			// Find matching PMs based on zipcode, pricing, and preferences
			$users = DB::table('user_preferences')
				->join('user_zipcodes', 'user_preferences.user_id', '=', 'user_zipcodes.user_id')
				->join('users', 'users.id', '=', 'user_preferences.user_id')
				->select('user_preferences.*', 'users.is_verified', 'users.credits')
				->where('user_preferences.pricing_id', $lead->category)
				->where('user_preferences.status', 1)
				->where('user_zipcodes.zipcode', $lead->zipcode)
				->where('user_zipcodes.status', 1)
				->where('users.status', 1) // Only active users
				->get();

			// Filter users based on property type preferences and unit count
			$matchingUsers = [];
			foreach($users as $user){
				$isMatch = true;

				// Check property type preference (if set)
				if(!empty($user->property_types)){
					$propertyTypes = json_decode($user->property_types, true);
					if(is_array($propertyTypes) && !in_array($lead->category, $propertyTypes)){
						$isMatch = false;
					}
				}

				// Check unit count preference (if set and lead has units field)
				if(isset($lead->units)){
					if(!empty($user->min_units) && $lead->units < $user->min_units){
						$isMatch = false;
					}
					if(!empty($user->max_units) && $lead->units > $user->max_units){
						$isMatch = false;
					}
				}

				if($isMatch){
					$matchingUsers[] = $user;
				}
			}

			// Sort matching users: verified PMs first, then by tier priority
			usort($matchingUsers, function($a, $b) {
				// Verified PMs get priority
				if($a->is_verified != $b->is_verified){
					return $b->is_verified <=> $a->is_verified;
				}
				// Then sort by tier_id (higher tier = higher priority)
				return $b->tier_id <=> $a->tier_id;
			});

			// Distribute lead to matching users
			foreach($matchingUsers as $user){
				// Calculate delivery time based on user preference
				$deliverySpeed = $user->preferred_delivery_speed ?? $user->timings;
				$sendTime = strtotime(date('Y-m-d H:i:s', strtotime('+' . $deliverySpeed . ' minutes', strtotime($lead->created_at))));

				// Check if it's time to send this lead
				if($currentTime >= $sendTime){
					// Check if lead is already assigned to this user
					$leadLinked = DB::table('user_leads')
						->where('user_id', $user->user_id)
						->where('lead_id', $lead->id)
						->where('status', 1)
						->first();

					if(!isset($leadLinked->id)){
						// Get tier pricing information
						$tierData = DB::table('tiers')
							->where('id', $user->tier_id)
							->where('status', 1)
							->first();

						if(isset($tierData->id)){
							// Check if tier has a price
							if($tierData->price > 0){
								// Verify PM has enough credits before assigning lead
								if($user->credits >= $tierData->price){
									// Assign lead to PM
									DB::table('user_leads')->insert([
										'user_id' => $user->user_id,
										'lead_id' => $lead->id,
										'status' => 1,
										'lead_unique_id' => $lead->unique_id,
										'property_type' => $lead->category,
										'lead_date' => $lead->created_at,
										'price' => $tierData->price,
										'tier_id' => $tierData->id,
										'created_at' => date('Y-m-d H:i:s'),
										'updated_at' => date('Y-m-d H:i:s')
									]);

									// Deduct credits from PM's account
									$remainingBalance = $user->credits - $tierData->price;
									DB::table('users')
										->where('id', $user->user_id)
										->update(['credits' => $remainingBalance]);

									$distributedCount++;
									Log::info('Lead distributed to PM', [
										'lead_id' => $lead->id,
										'user_id' => $user->user_id,
										'price' => $tierData->price,
										'remaining_credits' => $remainingBalance
									]);
								} else {
									Log::info('PM skipped - insufficient credits', [
										'lead_id' => $lead->id,
										'user_id' => $user->user_id,
										'required' => $tierData->price,
										'available' => $user->credits
									]);
								}
							} else {
								// Free tier - assign without credit check
								DB::table('user_leads')->insert([
									'user_id' => $user->user_id,
									'lead_id' => $lead->id,
									'status' => 1,
									'lead_unique_id' => $lead->unique_id,
									'property_type' => $lead->category,
									'lead_date' => $lead->created_at,
									'price' => $tierData->price,
									'tier_id' => $tierData->id,
									'created_at' => date('Y-m-d H:i:s'),
									'updated_at' => date('Y-m-d H:i:s')
								]);

								$distributedCount++;
								Log::info('Lead distributed to PM (free tier)', [
									'lead_id' => $lead->id,
									'user_id' => $user->user_id
								]);
							}
						}
					}
				}
			}
		}

		Log::info('Lead Distribution Completed', [
			'time' => date('Y-m-d H:i:s'),
			'leads_distributed' => $distributedCount
		]);

		$this->info("Lead distribution completed. {$distributedCount} leads distributed.");
		return 0;
    }
}