<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreferences extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'pricing_id',
        'tier_id',
        'is_active',
        'timings',
        'property_types',
        'min_units',
        'max_units',
        'preferred_delivery_speed',
        'email_notifications',
        'sms_notifications',
        'zip_codes',
        'service_radius_miles',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'property_types' => 'array',
        'zip_codes' => 'array',
        'is_active' => 'boolean',
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'min_units' => 'integer',
        'max_units' => 'integer',
        'timings' => 'integer',
        'preferred_delivery_speed' => 'integer',
        'service_radius_miles' => 'integer',
    ];

	/**
	 * Get the user (property manager) that owns this preference.
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * Get the tier associated with this preference.
	 */
	public function tier()
	{
		return $this->belongsTo(\App\Models\Tier::class);
	}

	/**
	 * Scope a query to only include active preferences.
	 */
	public function scopeActive($query)
	{
		return $query->where('is_active', true);
	}

	/**
	 * Check if user has any zip codes in their preferences.
	 */
	public function hasZipCodes()
	{
		return !empty($this->zip_codes) && is_array($this->zip_codes);
	}

	/**
	 * Check if a specific zip code is in user's preferences.
	 */
	public function hasZipCode($zipCode)
	{
		return $this->hasZipCodes() && in_array($zipCode, $this->zip_codes);
	}

	// Legacy methods for backward compatibility
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
}
