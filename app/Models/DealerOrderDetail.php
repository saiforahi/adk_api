<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerOrderDetail extends Model
{
    use HasFactory;
    protected $table="dealer_order_details";
    protected $guarded=[];

    public function dealer(){
        return $this->belongsTo(Dealer::class);
    }
}
