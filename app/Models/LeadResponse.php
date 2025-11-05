<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadResponse extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_lead_id',
        'pm_id',
        'lead_id',
        'response_type',
        'message',
        'contact_phone',
        'contact_email',
        'availability',
        'quoted_price',
        'notes',
        'responded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'availability' => 'array',
        'quoted_price' => 'decimal:2',
        'responded_at' => 'datetime',
    ];

    /**
     * Get the user lead that this response belongs to.
     */
    public function userLead()
    {
        return $this->belongsTo(UserLeads::class, 'user_lead_id');
    }

    /**
     * Get the property manager who responded.
     */
    public function propertyManager()
    {
        return $this->belongsTo(User::class, 'pm_id');
    }

    /**
     * Get the lead that was responded to.
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Scope responses by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('response_type', $type);
    }

    /**
     * Scope interested responses.
     */
    public function scopeInterested($query)
    {
        return $query->where('response_type', 'interested');
    }

    /**
     * Scope responses for a specific PM.
     */
    public function scopeByPropertyManager($query, $pmId)
    {
        return $query->where('pm_id', $pmId);
    }

    /**
     * Scope responses for a specific lead.
     */
    public function scopeForLead($query, $leadId)
    {
        return $query->where('lead_id', $leadId);
    }

    /**
     * Scope responses within a date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('responded_at', [$startDate, $endDate]);
    }

    /**
     * Check if response is positive (interested or contact requested).
     */
    public function isPositive()
    {
        return in_array($this->response_type, ['interested', 'contact_requested']);
    }
}
