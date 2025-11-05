<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StateProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'state_code',
        'name',
        'slug',
        'overview',
        'abbreviation',
        'meta_data',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'meta_data' => 'array',
    ];

    /**
     * Boot the model and set up event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from name if not provided
        static::creating(function ($state) {
            if (empty($state->slug)) {
                $state->slug = Str::slug($state->name);
            }
            // Auto-uppercase state_code
            if (!empty($state->state_code)) {
                $state->state_code = strtoupper($state->state_code);
            }
        });

        static::updating(function ($state) {
            if ($state->isDirty('name') && empty($state->slug)) {
                $state->slug = Str::slug($state->name);
            }
            if ($state->isDirty('state_code') && !empty($state->state_code)) {
                $state->state_code = strtoupper($state->state_code);
            }
        });
    }

    /**
     * Get all law contents for this state.
     */
    public function lawContents()
    {
        return $this->hasMany(StateLawContent::class, 'state_code', 'state_code');
    }

    /**
     * Get all document templates for this state.
     */
    public function documentTemplates()
    {
        return $this->hasMany(DocumentTemplate::class, 'state_code', 'state_code');
    }

    /**
     * Scope a query to only include active states.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to find by state code.
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('state_code', strtoupper($code));
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
