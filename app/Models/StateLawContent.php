<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StateLawContent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'state_code',
        'topic_slug',
        'title',
        'slug',
        'summary',
        'meta_description',
        'content',
        'is_published',
        'official_link',
        'last_updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_published' => 'boolean',
        'last_updated_at' => 'datetime',
    ];

    /**
     * Boot the model and set up event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from title if not provided
        static::creating(function ($content) {
            if (empty($content->slug)) {
                $content->slug = Str::slug($content->title);
            }
            // Auto-uppercase state_code
            if (!empty($content->state_code)) {
                $content->state_code = strtoupper($content->state_code);
            }
        });

        static::updating(function ($content) {
            if ($content->isDirty('title') && empty($content->slug)) {
                $content->slug = Str::slug($content->title);
            }
            if ($content->isDirty('state_code') && !empty($content->state_code)) {
                $content->state_code = strtoupper($content->state_code);
            }
        });
    }

    /**
     * Get the state profile that owns the law content.
     */
    public function stateProfile()
    {
        return $this->belongsTo(StateProfile::class, 'state_code', 'state_code');
    }

    /**
     * Get the legal topic that owns the law content.
     */
    public function legalTopic()
    {
        return $this->belongsTo(LegalTopic::class, 'topic_slug', 'slug');
    }

    /**
     * Scope a query to only include published content.
     */
    public function scopeActive($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope a query to filter by state code.
     */
    public function scopeByState($query, $stateCode)
    {
        return $query->where('state_code', strtoupper($stateCode));
    }

    /**
     * Scope a query to filter by topic.
     */
    public function scopeByTopic($query, $topicSlug)
    {
        return $query->where('topic_slug', $topicSlug);
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
