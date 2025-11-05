<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Lead extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zipcode',
        'property_type',
        'number_of_units',
        'square_footage',
        'year_built',
        'amenities',
        'price',
        'price_range',
        'category',
        'additional_services',
        'preferred_contact',
        'source',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'status',
        'quality_score',
        'distribution_count',
        'last_distributed_at',
        'viewed_count',
        'first_viewed_at',
        'unique_id',
        'confirmation_number',
        'questions',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'questions' => 'array',
        'amenities' => 'array',
        'price' => 'decimal:2',
        'number_of_units' => 'integer',
        'square_footage' => 'integer',
        'year_built' => 'integer',
        'quality_score' => 'integer',
        'distribution_count' => 'integer',
        'viewed_count' => 'integer',
        'last_distributed_at' => 'datetime',
        'first_viewed_at' => 'datetime',
    ];


	public function GetRecordById($id){
		return $this::where('id', $id)->first();
	}
	public function UpdateRecord($Details){
		$Record = $this::where('id', $Details['id'])->update($Details);
		return true;
	}
	public function CreateRecord($Details){
		$Record = $this::create($Details);
		return $Record;
	}

    /**
     * Get the owner that owns the lead.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    /**
     * Get the property managers this lead was distributed to.
     */
    public function userLeads()
    {
        return $this->hasMany(UserLeads::class, 'lead_id');
    }

    /**
     * Get the responses for this lead.
     */
    public function responses()
    {
        return $this->hasMany(Response::class, 'lead_id');
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by state.
     */
    public function scopeByState($query, $state)
    {
        return $query->where('state', $state);
    }

    /**
     * Scope a query to filter by zipcode.
     */
    public function scopeByZipcode($query, $zipcode)
    {
        return $query->where('zipcode', $zipcode);
    }

    /**
     * Scope a query to get high quality leads.
     */
    public function scopeHighQuality($query, $minScore = 70)
    {
        return $query->where('quality_score', '>=', $minScore);
    }

    /**
     * Scope a query to get undistributed leads.
     */
    public function scopeUndistributed($query)
    {
        return $query->where('distribution_count', 0);
    }

    /**
     * Increment the distribution counter.
     */
    public function markAsDistributed()
    {
        $this->increment('distribution_count');
        $this->update(['last_distributed_at' => now()]);
    }

    /**
     * Increment the viewed counter.
     */
    public function markAsViewed()
    {
        $this->increment('viewed_count');
        if (!$this->first_viewed_at) {
            $this->update(['first_viewed_at' => now()]);
        }
    }

    /**
     * Calculate and update the quality score based on lead completeness.
     */
    public function calculateQualityScore()
    {
        $score = 0;

        // Basic contact info (30 points)
        if ($this->name) $score += 10;
        if ($this->email) $score += 10;
        if ($this->phone) $score += 10;

        // Property details (40 points)
        if ($this->property_type) $score += 10;
        if ($this->number_of_units) $score += 10;
        if ($this->square_footage) $score += 10;
        if ($this->price || $this->price_range) $score += 10;

        // Additional info (30 points)
        if ($this->additional_services) $score += 10;
        if (!empty($this->amenities)) $score += 10;
        if ($this->year_built) $score += 10;

        $this->update(['quality_score' => $score]);
        return $score;
    }
}
