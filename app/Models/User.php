<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\ResetPasswordNotification;

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
        ]);
    }
}
