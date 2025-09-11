<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'token',
        'token_hash',
        'role_id',
        'ip_address',
        'user_agent',
        'last_activity',
        'is_active',
    ];

    protected $casts = [
        'last_activity' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    /**
     * Get the user that owns the session.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get only active sessions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Deactivate the session.
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Update last activity timestamp.
     */
    public function updateActivity()
    {
        $this->update(['last_activity' => now()]);
    }
}
