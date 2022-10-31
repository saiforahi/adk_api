<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TycoonWallet extends Model
{
    use HasFactory;
    protected $table="tycoon_wallets";
    protected $guarded=[];
    protected $casts = [
        'created_at'=>'datetime:Y-m-d h:i:s A',
        'updated_at'=>'datetime:Y-m-d h:i:s A',
        'deleted_at'=>'datetime:Y-m-d h:i:s A'
    ];

    public function tycoon(){
        return $this->belongsTo(Tycoon::class);
    }
}
