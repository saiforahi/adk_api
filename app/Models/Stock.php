<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'dealer_id',
        'unit_type',
        'unit_value',
        'stock_place',
        'stock_in',
        'stock_out'
    ];
    protected $casts = [
    ];
}
