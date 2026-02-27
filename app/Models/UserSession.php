<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property string $token
 * @property string $token_hash
 * @property string $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $last_activity
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereLastActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereTokenHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereUuid($value)
 * @mixin \Eloquent
 */
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
