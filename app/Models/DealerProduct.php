<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerProduct extends Model
{
    use HasFactory;
    protected $table="dealer_products";
    protected $guarded=[];

    public function dealer(){
        return $this->belongsTo(Dealer::class);
    }
}
