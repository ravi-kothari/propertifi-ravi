<?php

namespace App\Mail;

use App\Models\UserLeads;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeadDistributeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userLead;
    public $pm;
    public $lead;
    public $matchScore;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\UserLeads|array  $data
     * @return void
     */
    public function __construct($data)
    {
        // Support both new (UserLeads object) and legacy (array) formats
        if ($data instanceof UserLeads) {
            $this->userLead = $data;
            $this->pm = $data->propertyManager;
            $this->lead = $data->lead;
            $this->matchScore = $data->match_score;
        } else {
            // Legacy support for array format
            $this->data = $data;
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // New format with UserLeads object
        if (isset($this->userLead)) {
            $propertyType = $this->lead->property_type ?? 'Property';
            $location = trim(($this->lead->city ?? '') . ', ' . ($this->lead->state ?? ''));

            return $this->subject("New Lead Match: {$propertyType} in {$location}")
                ->view('mail.leadDistributeMail')
                ->with([
                    'pm_name' => $this->pm->name,
                    'lead' => $this->lead,
                    'match_score' => $this->matchScore,
                    'property_type' => $propertyType,
                    'location' => $location,
                    'quality_score' => $this->lead->quality_score,
                ]);
        }

        // Legacy format support
        return $this->subject($this->data['subject'] ?? 'New Lead Distribution')
            ->view('mail.leadDistributeMail')
            ->with(['data' => $this->data]);
    }
}
