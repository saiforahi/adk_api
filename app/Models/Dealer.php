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

class Dealer extends Authenticatable implements HasMedia,MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable,InteractsWithMedia,HasRoles;
    protected $guard_name = 'dealer';
    protected $guarded=[];
    // returning guard name
    public function guard__name(){
        return $this->guard_name;
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
}
