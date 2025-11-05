<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Tier Model (Singular alias for Tiers model)
 *
 * This is an alias to match Laravel naming conventions.
 * The actual table is 'tiers' (plural).
 */
class Tier extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tiers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'title',
        'price',
        'timings',
        'priority',
        'lead_cap',
        'exclusivity_hours',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'priority' => 'integer',
        'lead_cap' => 'integer',
        'exclusivity_hours' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Get the user preferences associated with this tier.
     */
    public function userPreferences()
    {
        return $this->hasMany(UserPreferences::class, 'tier_id');
    }

    /**
     * Scope a query to only include active tiers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope a query to order by priority (highest first).
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }
}
