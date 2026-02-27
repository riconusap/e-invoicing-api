<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\ResetPasswordNotification;

/**
 * @method void cleanExpiredSessions()
 * @method bool hasActiveSessions()
 * @method int getActiveSessionsCount()
 * @method void updateLastLogin(string|null $ip = null)
 * @method \App\Models\UserSession createSession(string $token, string|null $ip = null, string|null $userAgent = null)
 * @method void logoutFromAllDevices()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany sessions()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany activeSessions()
 * @property int $id
 * @property string $name
 * @property int|null $created_by
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property string|null $last_login_ip
 * @property int $role_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserSession> $activeSessions
 * @property-read int|null $active_sessions_count
 * @property-read \App\Models\Employee|null $employee
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Role $role
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserSession> $sessions
 * @property-read int|null $sessions_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 * @mixin \Eloquent
 */
class User extends Authenticatable implements JWTSubject // <-- Implementasikan di sini
{
    use HasFactory, Notifiable, SoftDeletes; // <-- Tambahkan SoftDeletes

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'username',
        'email',
        'role_id',
        'password',
        'employee_id',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'id',
        'verification_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    // VVV Tambahkan dua method di bawah ini VVV

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the employee record associated with the user.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the employee record associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the user sessions.
     */
    public function sessions()
    {
        return $this->hasMany(UserSession::class);
    }

    /**
     * Get active user sessions.
     */
    public function activeSessions()
    {
        return $this->hasMany(UserSession::class)->active();
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role && strtolower($this->role->name) === 'admin';
    }

    /**
     * Check if user is currently logged in (has valid token)
     */
    public function isLoggedIn()
    {
        return auth('api')->check() && auth('api')->user()->id === $this->id;
    }

    /**
     * Update last login information
     */
    public function updateLastLogin($ip = null)
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip ?? request()->ip(),
        ]);
    }

    /**
     * Check if user has active sessions
     */
    public function hasActiveSessions()
    {
        return $this->activeSessions()->exists();
    }

    /**
     * Get active sessions count
     */
    public function getActiveSessionsCount()
    {
        return $this->activeSessions()->count();
    }

    /**
     * Logout from all devices
     */
    public function logoutFromAllDevices()
    {
        $this->activeSessions()->update(['is_active' => false]);
    }

    /**
     * Create a new session
     */
    public function createSession($token, $ip = null, $userAgent = null)
    {
        return $this->sessions()->create([
            'token' => $token,
            'token_hash' => hash('sha256', $token),
            'ip_address' => $ip ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
            'last_activity' => now(),
            'is_active' => true,
        ]);
    }

    /**
     * Clean up expired sessions
     */
    public function cleanExpiredSessions()
    {
        // Get JWT TTL from config (default 60 minutes)
        $jwtTtl = config('jwt.ttl', 60);

        // Deactivate sessions older than JWT TTL
        $this->sessions()
            ->where('is_active', true)
            ->where('last_activity', '<', now()->subMinutes($jwtTtl))
            ->update(['is_active' => false]);
    }
}
