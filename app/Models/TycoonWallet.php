<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TycoonWallet extends Model
{
    use HasFactory;
    protected $table="tycoon_wallets";
    protected $guarded=[];

    public function tycoon(){
        return $this->belongsTo(Tycoon::class);
    }
}
