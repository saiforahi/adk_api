<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TopupRequest extends Model
{
    use HasFactory;
    protected $table="topup_requests";
    protected $guarded=[];
    protected $with=["dealer"];
    protected $hidden=["document","request_from_type","request_from_id"];
    protected $casts = [
        'created_at'=>'datetime:Y-m-d h:i:s A',
        'updated_at'=>'datetime:Y-m-d h:i:s A',
        'deleted_at'=>'datetime:Y-m-d h:i:s A'
    ];

    public function dealer():BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    public function request_from()
    {
        return $this->morphTo();
    }
}
