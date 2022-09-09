<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tycoon extends Authenticatable implements HasMedia,MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable,InteractsWithMedia,HasRoles,SoftDeletes;
    protected $guard_name = 'tycoon';
    protected $table='tycoons';
    protected $guarded=[];
    protected $hidden=['password','remember_token'];
    // protected $with = ['wallet','guard__name'];
    protected $casts = [
        'email_verified_at' => 'datetime:Y-m-d h:i:s A',
        'created_at'=>'datetime:Y-m-d h:i:s A',
        'updated_at'=>'datetime:Y-m-d h:i:s A',
        'deleted_at'=>'datetime:Y-m-d h:i:s A'
    ];
    // returning guard name
    public function guard__name(){
        return $this->guard_name;
    }
    // polymorphic relation to product stock order table
    public function product_stock_orders(){
        return $this->morphMany(ProductStockOrder::class, 'order_from');
    }
    public function product_stock_orders_to_me(){
        return $this->morphMany(ProductStockOrder::class, 'order_to');
    }
    // media functions
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('profile_pic')
              ->width(247)
              ->height(300)
              ->sharpen(10)
              ->queued();
    }
    public function registerMediaCollections(): void
    {
        // $this->addMediaCollection('thumb')->useDisk('public')->acceptsMimeTypes(['image/jpeg','image/jpg','image/png','image/webp'])->withResponsiveImages();
        $this
            ->addMediaCollection('profile_pic')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg','image/jpg','image/png','image/webp'])
            ->withResponsiveImages();
    }
    public function wallet(){
        return $this->hasOne(TycoonWallet::class);
    }
}
