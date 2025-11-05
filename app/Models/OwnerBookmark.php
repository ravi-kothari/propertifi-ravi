<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OwnerBookmark extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'owner_id',
        'bookmarkable_id',
        'bookmarkable_type',
    ];

    /**
     * Get the parent bookmarkable model (StateLawContent or DocumentTemplate).
     */
    public function bookmarkable()
    {
        return $this->morphTo();
    }
}
