<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadFeedback extends Model
{
    use HasFactory;

    protected $table = 'lead_feedback';

    protected $fillable = [
        'user_lead_id',
        'pm_id',
        'lead_id',
        'feedback_type',
        'feedback_notes',
        'rejection_reason',
        'quality_rating',
    ];

    protected $casts = [
        'quality_rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user lead distribution record.
     */
    public function userLead()
    {
        return $this->belongsTo(UserLeads::class, 'user_lead_id');
    }

    /**
     * Get the property manager who provided feedback.
     */
    public function propertyManager()
    {
        return $this->belongsTo(User::class, 'pm_id');
    }

    /**
     * Get the lead this feedback is about.
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Scope to get positive feedback only.
     */
    public function scopePositive($query)
    {
        return $query->whereIn('feedback_type', ['accepted']);
    }

    /**
     * Scope to get negative feedback only.
     */
    public function scopeNegative($query)
    {
        return $query->whereIn('feedback_type', ['rejected', 'spam', 'unresponsive', 'not_interested', 'low_quality']);
    }

    /**
     * Scope to filter by PM.
     */
    public function scopeForPM($query, $pmId)
    {
        return $query->where('pm_id', $pmId);
    }
}
