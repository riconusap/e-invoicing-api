<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $uuid
 * @property int $placement_id
 * @property int $contract_value
 * @property string $start_on
 * @property string $ends_on
 * @property string $project_type
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\Placement $placement
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractClient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractClient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractClient onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractClient query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractClient whereContractValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractClient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractClient whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractClient whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractClient whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractClient whereEndsOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractClient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractClient wherePlacementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractClient whereProjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractClient whereStartOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractClient whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractClient whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractClient whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractClient withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractClient withoutTrashed()
 * @mixin \Eloquent
 */
class ContractClient extends Model
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
        'placement_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [
        'id',
    ];

    /**
     * Get the placement that the contract client belongs to.
     */
    public function placement()
    {
        return $this->belongsTo(Placement::class);
    }

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
