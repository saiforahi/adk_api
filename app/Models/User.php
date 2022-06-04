<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements HasMedia,MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable,InteractsWithMedia,HasRoles,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime:Y-m-d h:i:s A',
        'created_at'=>'datetime:Y-m-d h:i:s A',
        'updated_at'=>'datetime:Y-m-d h:i:s A',
        'deleted_at'=>'datetime:Y-m-d h:i:s A'
    ];
    protected $guard_name = 'staff';
    // returning guard name
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
}
