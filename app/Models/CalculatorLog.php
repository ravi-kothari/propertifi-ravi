<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalculatorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'calculator_type',
        'input_data',
        'result_data',
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'input_data' => 'array',
        'result_data' => 'array',
    ];

    /**
     * Get the user who used the calculator (if logged in)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for specific calculator type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('calculator_type', $type);
    }

    /**
     * Get popular calculators
     */
    public static function getPopularCalculators($limit = 3)
    {
        return self::selectRaw('calculator_type, COUNT(*) as usage_count')
            ->groupBy('calculator_type')
            ->orderByDesc('usage_count')
            ->limit($limit)
            ->get();
    }
}
