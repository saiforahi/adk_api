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
 * @property string $name
 * @property boolean $featured
 * @property boolean $digital
 */
class PurchaseOrderDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'req_quantity',
        'cost',
        'total_amount'
    ];
    protected $casts = [
        'created_at'=>'datetime:Y-m-d h:i:s A',
        'updated_at'=>'datetime:Y-m-d h:i:s A',
        'deleted_at'=>'datetime:Y-m-d h:i:s A',
    ];
    /**
     * @return HasMany
     */

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
