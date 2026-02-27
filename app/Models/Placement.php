<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string $description
 * @property int $client_id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Client $client
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContractClient> $contractClients
 * @property-read int|null $contract_clients_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\PicExternal|null $picExternal
 * @property-read \App\Models\Employee|null $picInternal
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Database\Factories\PlacementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Placement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Placement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Placement onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Placement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Placement whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Placement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Placement whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Placement whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Placement whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Placement whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Placement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Placement whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Placement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Placement whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Placement whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Placement withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Placement withoutTrashed()
 * @mixin \Eloquent
 */
class Placement extends Model
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
        'client_id',
        'pic_external_id',
        'pic_internal_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [
        'id',
    ];

    /**
     * Get the client that owns the placement.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the external PIC for the placement.
     */
    public function picExternal()
    {
        return $this->belongsTo(PicExternal::class);
    }

    /**
     * Get the internal PIC (employee) for the placement.
     */
    public function picInternal()
    {
        return $this->belongsTo(Employee::class, 'pic_internal_id');
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

    /**
     * Get the contract clients for the placement.
     */
    public function contractClients()
    {
        return $this->hasMany(ContractClient::class);
    }
}
