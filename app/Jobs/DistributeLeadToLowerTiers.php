<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Services\LeadDistributionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DistributeLeadToLowerTiers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $leadId;
    protected $options;

    /**
     * Create a new job instance.
     *
     * @param int $leadId
     * @param array $options
     * @return void
     */
    public function __construct($leadId, array $options = [])
    {
        $this->leadId = $leadId;
        $this->options = $options;
    }

    /**
     * Execute the job.
     * Distributes the lead to lower-tier PMs after exclusivity period expires.
     *
     * @param LeadDistributionService $distributionService
     * @return void
     */
    public function handle(LeadDistributionService $distributionService)
    {
        $lead = Lead::find($this->leadId);

        if (!$lead) {
            Log::warning("Lead {$this->leadId} not found for lower-tier distribution");
            return;
        }

        // Disable exclusivity enforcement for this distribution
        // This allows lower tiers to receive the lead after the exclusivity period
        $result = $distributionService->distributeLead($lead, array_merge($this->options, [
            'respect_exclusivity' => false,
        ]));

        if ($result['success']) {
            Log::info("Lead {$this->leadId} distributed to {$result['distributed_count']} lower-tier PMs after exclusivity period");
        } else {
            Log::error("Failed to distribute lead {$this->leadId} to lower tiers: " . $result['message']);
        }
    }
}
