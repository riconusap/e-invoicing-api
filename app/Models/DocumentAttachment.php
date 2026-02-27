<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $uuid
 * @property string $file
 * @property string $filename
 * @property string $file_type
 * @property int $invoice_id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $deletedBy
 * @property-read \App\Models\Invoice $invoice
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentAttachment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentAttachment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentAttachment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentAttachment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentAttachment whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentAttachment whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentAttachment whereFileType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentAttachment whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentAttachment whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentAttachment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentAttachment whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentAttachment whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentAttachment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentAttachment withoutTrashed()
 * @mixin \Eloquent
 */
class DocumentAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'file',
        'filename',
        'file_type',
        'invoice_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
} 