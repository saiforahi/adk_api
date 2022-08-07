<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DealerProductStock extends Model
{
    use HasFactory;
    protected $table="dealer_product_stocks";
    protected $guarded=[];
    protected $with=["dealer","product"];
    protected $casts = [
        'created_at'=>'datetime:Y-m-d h:i:s A',
        'updated_at'=>'datetime:Y-m-d h:i:s A',
        'deleted_at'=>'datetime:Y-m-d h:i:s A'
    ];

    public function dealer():BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }
    public function product():BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
}
