<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'state_code',
        'category_slug',
        'file_path',
        'file_size_mb',
        'download_count',
        'tags',
        'is_free',
        'requires_signup',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_free' => 'boolean',
        'requires_signup' => 'boolean',
        'is_active' => 'boolean',
        'download_count' => 'integer',
        'file_size_mb' => 'decimal:2',
        'tags' => 'array',
    ];

    /**
     * Boot the model and set up event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-uppercase state_code
        static::creating(function ($template) {
            if (!empty($template->state_code)) {
                $template->state_code = strtoupper($template->state_code);
            }
        });

        static::updating(function ($template) {
            if ($template->isDirty('state_code') && !empty($template->state_code)) {
                $template->state_code = strtoupper($template->state_code);
            }
        });
    }

    /**
     * Get the state profile that owns the document template.
     */
    public function stateProfile()
    {
        return $this->belongsTo(StateProfile::class, 'state_code', 'state_code');
    }

    /**
     * Get the document category that owns the template.
     */
    public function documentCategory()
    {
        return $this->belongsTo(DocumentCategory::class, 'category_slug', 'slug');
    }

    /**
     * Scope a query to only include active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include free templates.
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    /**
     * Scope a query to filter by state code.
     */
    public function scopeByState($query, $stateCode)
    {
        return $query->where('state_code', strtoupper($stateCode));
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $categorySlug)
    {
        return $query->where('category_slug', $categorySlug);
    }

    /**
     * Scope a query to order templates by download count.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('download_count', 'desc')->orderBy('title');
    }

    /**
     * Get all downloads for this template.
     */
    public function downloads()
    {
        return $this->hasMany(TemplateDownload::class, 'template_id');
    }

    /**
     * Increment the download count.
     */
    public function incrementDownloads()
    {
        $this->increment('download_count');
    }

    /**
     * Record a new download with tracking information.
     */
    public function recordDownload($userId = null, $request = null)
    {
        $download = TemplateDownload::create([
            'template_id' => $this->id,
            'user_id' => $userId,
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? $request->userAgent() : null,
            'session_id' => $request ? $request->session()->getId() : null,
            'requires_signup' => $this->requires_signup,
            'downloaded_at' => now(),
        ]);

        $this->incrementDownloads();

        return $download;
    }
}
