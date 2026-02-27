<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string|null $logo
 * @property string $address
 * @property string $phone
 * @property string $email
 * @property string $pic_name
 * @property string $pic_phone
 * @property string $pic_email
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $deletedBy
 * @property-read mixed $logo_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Placement> $placements
 * @property-read int|null $placements_count
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Database\Factories\ClientFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client wherePicEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client wherePicName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client wherePicPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client withoutTrashed()
 * @mixin \Eloquent
 */
class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clients';

    protected $fillable = [
        'uuid',
        'name',
        'logo',
        'address',
        'phone',
        'email',
        'pic_name',
        'pic_phone',
        'pic_email',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $hidden = [
        'id',
    ];

    protected $appends = ['logo_url'];

    // Relationships
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function placements()
    {
        return $this->hasMany(Placement::class);
    }

    // Accessor for logo URL
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return null;
    }
}
