<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string $position
 * @property string $phone
 * @property string $email
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Database\Factories\PicExternalFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicExternal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicExternal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicExternal onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicExternal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicExternal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicExternal whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicExternal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicExternal whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicExternal whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicExternal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicExternal whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicExternal wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicExternal wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicExternal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicExternal whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicExternal whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicExternal withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PicExternal withoutTrashed()
 * @mixin \Eloquent
 */
class PicExternal extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'phone',
        'email',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [
        'id',
    ];

    /**
     * Get the user who created the placement.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the placement.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who deleted the placement.
     */
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

}
