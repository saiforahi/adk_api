<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductStockOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $casts = [
        'created_at'=>'datetime:Y-m-d h:i:s A',
        'updated_at'=>'datetime:Y-m-d h:i:s A',
        'deleted_at'=>'datetime:Y-m-d h:i:s A'
    ];
    protected $hidden = [
        'order_from_type','order_from_id','order_to_type','order_to_id'
    ];
    protected $appends = [
        'from_type','from_dealer_type','to_type','to_dealer_type'
    ];
    /**
     * @return BelongsTo
     */
    
    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
    public function order_from()
    {
        return $this->morphTo();
    }
    public function order_to()
    {
        return $this->morphTo();
    }

    public function getFromTypeAttribute(){
        $type="";
        switch($this->order_from_type){
            case 'App\Models\Dealer':
                $type='Dealer';
                break;
            case 'App\Models\Admin':
                $type='Admin';break;
            case 'App\Models\Tycoon':
                $type='Tycoon';break;
            case 'App\Models\MasterTycoon':
                $type='Master Tycoon';break;
        }
        return $type;
    }
    public function getToTypeAttribute(){
        $type="";
        switch($this->order_to_type){
            case 'App\Models\Dealer':
                $type='Dealer';
                break;
            case 'App\Models\Admin':
                $type='Admin';break;
            case 'App\Models\Tycoon':
                $type='Tycoon';break;
            case 'App\Models\MasterTycoon':
                $type='Master Tycoon';break;
        }
        return $type;
    }
    public function getFromDealerTypeAttribute(){
        $type="";
        if(!empty($this->order_from)){
            switch($this->from_type){
                case 'Dealer':
                    $type=DealerType::where('id',$this->order_from->dealer_type_id)->first()->name;
                    break;
            }
        }
        
        return $type;
    }
    public function getToDealerTypeAttribute(){
        $type="";
        if(!empty($this->order_to)){
            switch($this->to_type){
                case 'Dealer':
                    $type=DealerType::where('id',$this->order_to->dealer_type_id)->first()->name;
                    break;
            }
        }
        
        return $type;
    }
}
