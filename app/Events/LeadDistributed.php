<?php

namespace App\Events;

use App\Models\UserLeads;
use App\Models\Lead;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadDistributed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userLead;
    public $score;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\UserLeads  $userLead
     * @param  array  $score
     * @return void
     */
    public function __construct(UserLeads $userLead, array $score = [])
    {
        $this->userLead = $userLead;
        $this->score = $score;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('property-manager.' . $this->userLead->pm_id);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        $lead = $this->userLead->lead;

        return [
            'lead' => [
                'id' => $lead->id,
                'user_lead_id' => $this->userLead->id,
                'property_type' => $lead->property_type,
                'street_address' => $lead->street_address ?? $lead->address ?? '',
                'city' => $lead->city,
                'state' => $lead->state,
                'zip_code' => $lead->zipcode ?? $lead->zip_code ?? '',
                'number_of_units' => $lead->number_of_units,
                'full_name' => $lead->name ?? $lead->full_name ?? '',
                'created_at' => $lead->created_at->toISOString(),
                'status' => $this->userLead->status ?? 'new',
            ],
            'score' => $this->score,
            'notification' => [
                'title' => ($this->score['tier'] ?? '') === 'excellent' ? '🔥 High-Value Lead!' : '⭐ New Lead Available',
                'message' => "New {$lead->property_type} property in {$lead->city}, {$lead->state}",
                'priority' => $this->score['badge']['priority'] ?? 'normal',
            ],
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'lead.distributed';
    }
}
