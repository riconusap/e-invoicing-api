<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $uuid
 * @property string $file
 * @property string $filename
 * @property int $employee_id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\Employee $employee
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmployeeDocument withoutTrashed()
 * @mixin \Eloquent
 */
class EmployeeDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'employee_id',
        'file',
        'filename',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $hidden = [
        'id',
        'employee_id'
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
}
