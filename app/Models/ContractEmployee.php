<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
