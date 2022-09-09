<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BalanceTransfer extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    protected $casts = [
        'created_at'=>'datetime:Y-m-d h:i:s A',
        'updated_at'=>'datetime:Y-m-d h:i:s A',
        'deleted_at'=>'datetime:Y-m-d h:i:s A'
    ];

    protected $hidden = [
        'transfer_from_type','transfer_from_id','transfer_to_type','transfer_to_id'
    ];

    public function transfer_from()
    {
        return $this->morphTo();
    }
    public function transfer_to()
    {
        return $this->morphTo();
    }

}
