<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerProductStock extends Model
{
    use HasFactory;
    protected $table="dealer_product_stocks";
    protected $guarded=[];

    public function dealer(){
        return $this->belongsTo(Dealer::class);
    }
}
