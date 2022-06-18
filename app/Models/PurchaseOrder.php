<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PurchaseOrderDetail
 * @package App\Models
 * @property int $id
 * @property string $po_no
 * @property string $purchase_date
 * @property int $supplier_id
 * @property int $warehouse_id
 * @property string $remarks
 */
class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'po_no',
        'purchase_date',
        'supplier_id',
        'warehouse_id',
        'remarks' 
    ];

    /**
     * @return HasMany
     */
    public function details(): HasMany
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }
}
