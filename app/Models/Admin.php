<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable implements HasMedia,MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable,InteractsWithMedia,HasRoles,SoftDeletes;
    protected $guard_name = 'admin';
    protected $guarded=[];
    protected $hidden=['password','remember_token'];
    protected $casts = [
        'email_verified_at' => 'datetime:Y-m-d h:i:s A',
        'created_at'=>'datetime:Y-m-d h:i:s A',
        'updated_at'=>'datetime:Y-m-d h:i:s A',
        'deleted_at'=>'datetime:Y-m-d h:i:s A'
    ];
    // media functions
    public function guard__name(){
        return $this->guard_name;
    }
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
        return $this->hasOne(AdminWallet::class);
    }
}
