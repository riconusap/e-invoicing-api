<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'full_name',
        'nik',
        'created_by',
        'updated_by',
        'deleted_by'
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
