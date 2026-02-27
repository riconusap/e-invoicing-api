<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $uuid
 * @property string|null $nip
 * @property \Illuminate\Support\Carbon $start_on
 * @property \Illuminate\Support\Carbon $ends_on
 * @property int $thp
 * @property int $daily_wages
 * @property string $account_number
 * @property string $bank_id
 * @property string $account_holder_name
 * @property string|null $no_bpjstk
 * @property string|null $no_bpjskes
 * @property int $employee_id
 * @property int $placement_id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\Employee $employee
 * @property-read \App\Models\Placement $placement
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee whereAccountHolderName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee whereDailyWages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee whereEndsOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee whereNip($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee whereNoBpjskes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee whereNoBpjstk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee wherePlacementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee whereStartOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee whereThp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractEmployee withoutTrashed()
 * @mixin \Eloquent
 */
class ContractEmployee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'nip',
        'start_on',
        'ends_on',
        'thp',
        'daily_wages',
        'account_number',
        'bank_id',
        'account_holder_name',
        'no_bpjstk',
        'no_bpjskes',
        'employee_id',
        'placement_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [
        'id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_on' => 'date',
        'ends_on' => 'date',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

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

    public function placement()
    {
        return $this->belongsTo(Placement::class);
    }
}
