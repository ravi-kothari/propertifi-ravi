<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedCalculation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'owner_id',
        'calculator_type',
        'input_data',
        'result_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'input_data' => 'array',
        'result_data' => 'array',
    ];

    /**
     * Get the owner that owns the saved calculation.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}
