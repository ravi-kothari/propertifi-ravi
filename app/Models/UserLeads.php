<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLeads extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_leads';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'pm_id',
        'lead_id',
        'status',
        'lead_unique_id',
        'property_type',
        'lead_date',
        'price',
        'tier_id',
        'delivery_speed_preference',
        'location_match',
        'category_match',
        'distribution_fairness',
        'tier_preference',
        'total_score',
        'match_score',
        'distributed_at',
        'viewed_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'match_score' => 'integer',
        'distributed_at' => 'datetime',
        'viewed_at' => 'datetime',
    ];


    /**
     * Get the property manager that owns this user lead.
     */
    public function propertyManager()
    {
        return $this->belongsTo(User::class, 'pm_id');
    }

    /**
     * Get the lead associated with this user lead.
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    /**
     * Get the responses for this user lead.
     */
    public function responses()
    {
        return $this->hasMany(LeadResponse::class, 'user_lead_id');
    }

    // Legacy methods for backward compatibility
    public function GetRecordById($id)
    {
        return $this::where('id', $id)->first();
    }

    public function UpdateRecord($Details)
    {
        $Record = $this::where('id', $Details['id'])->update($Details);
        return true;
    }

    public function CreateRecord($Details)
    {
        $Record = $this::create($Details);
        return $Record;
    }
}
