<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $uuid
 * @property string $full_name
 * @property string $nik
 * @property string $nip
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContractEmployee> $contractEmployees
 * @property-read int|null $contract_employees_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EmployeeDocument> $employeeDocuments
 * @property-read int|null $employee_documents_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Placement> $placements
 * @property-read int|null $placements_count
 * @property-read \App\Models\User|null $updatedBy
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\EmployeeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereNik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereNip($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee withoutTrashed()
 * @mixin \Eloquent
 */
class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'full_name',
        'nik',
        'nip',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $hidden = [
        'id',
    ];

    // Relationships
    public function user()
    {
        return $this->hasOne(User::class);
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

    public function employeeDocuments()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function contractEmployees()
    {
        return $this->hasMany(ContractEmployee::class);
    }

    public function placements()
    {
        return $this->hasMany(Placement::class, 'pic_internal_id');
    }
}
