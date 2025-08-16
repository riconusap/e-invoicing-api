<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
