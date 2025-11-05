<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Owner extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the leads for the owner.
     */
    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Get the bookmarks for the owner.
     */
    public function bookmarks()
    {
        return $this->hasMany(OwnerBookmark::class);
    }

    /**
     * Get the saved calculations for the owner.
     */
    public function savedCalculations()
    {
        return $this->hasMany(SavedCalculation::class);
    }
}
