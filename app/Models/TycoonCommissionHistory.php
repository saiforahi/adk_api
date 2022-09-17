<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TycoonCommissionHistory extends Model
{
    use HasFactory;
    protected $guarded=[];


    public function from_tycoon()
    {
        return $this->belongsTo(Tycoon::class, 'from_tycoon_id');
    }
    public function to_tycoon()
    {
        return $this->belongsTo(Tycoon::class, 'to_tycoon_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
