<?php

namespace App\Listeners;

use App\Events\LeadDistributed;
use App\Mail\LeadDistributeMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendLeadDistributionNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\LeadDistributed  $event
     * @return void
     */
    public function handle(LeadDistributed $event)
    {
        $userLead = $event->userLead;

        // Load relationships
        $userLead->load(['propertyManager', 'lead']);

        $pm = $userLead->propertyManager;
        $lead = $userLead->lead;

        // Check if PM wants email notifications
        $preferences = $pm->preferences;
        if (!$preferences || !$preferences->email_notifications) {
            Log::info("Skipping email notification for PM {$pm->id} - email notifications disabled");
            return;
        }

        try {
            // Send email notification
            Mail::to($pm->email)->send(new LeadDistributeMail($userLead));

            Log::info("Lead distribution email sent to PM {$pm->id} ({$pm->email}) for lead {$lead->id}");

            // TODO: If SMS notifications are enabled, send SMS
            if ($preferences->sms_notifications && $pm->mobile) {
                // Implement SMS notification using Twilio or similar service
                // $this->sendSmsNotification($pm, $lead);
                Log::info("SMS notification would be sent to PM {$pm->id} ({$pm->mobile})");
            }

        } catch (\Exception $e) {
            Log::error("Failed to send lead distribution notification to PM {$pm->id}: " . $e->getMessage());

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \App\Events\LeadDistributed  $event
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(LeadDistributed $event, $exception)
    {
        Log::error("Lead distribution notification failed permanently for UserLead {$event->userLead->id}: " . $exception->getMessage());
    }
}
