<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateDownload extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'template_id',
        'user_id',
        'ip_address',
        'user_agent',
        'session_id',
        'requires_signup',
        'downloaded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'requires_signup' => 'boolean',
        'downloaded_at' => 'datetime',
    ];

    /**
     * Get the template that was downloaded.
     */
    public function template()
    {
        return $this->belongsTo(DocumentTemplate::class, 'template_id');
    }

    /**
     * Get the user who downloaded the template.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope downloads for a specific template.
     */
    public function scopeForTemplate($query, $templateId)
    {
        return $query->where('template_id', $templateId);
    }

    /**
     * Scope downloads by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope downloads from a specific IP.
     */
    public function scopeByIp($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }

    /**
     * Get downloads within a date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('downloaded_at', [$startDate, $endDate]);
    }

    /**
     * Get unique download count (by IP and user).
     */
    public static function getUniqueDownloads($templateId)
    {
        return static::forTemplate($templateId)
            ->selectRaw('COUNT(DISTINCT COALESCE(user_id, ip_address)) as unique_count')
            ->value('unique_count');
    }
}
