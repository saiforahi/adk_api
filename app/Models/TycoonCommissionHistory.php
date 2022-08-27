<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TycoonCommissionHistory extends Model
{
    use HasFactory;
    protected $guarded=[];


    public function tycoon()
    {
        return $this->belongsTo(Tycoon::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
