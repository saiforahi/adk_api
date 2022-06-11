<?php

namespace App\Models;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Supplier extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia,SoftDeletes;
    protected $guarded=[];
    protected $casts = [
        'created_at'=>'datetime:Y-m-d h:i:s A',
        'updated_at'=>'datetime:Y-m-d h:i:s A',
        'deleted_at'=>'datetime:Y-m-d h:i:s A',
        'company'=>'array'
    ];
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
              ->width(368)
              ->height(232)
              ->sharpen(10)->queued();;

        $this->addMediaConversion('old-picture')
              ->sepia()
              ->border(10, 'black', Manipulations::BORDER_OVERLAY)->queued();;
              
        $this->addMediaConversion('thumb-cropped')
            ->crop('crop-center', 400, 400)->queued();; // Trim or crop the image to the center for specified width and height.
    }
    public function registerMediaCollections(): void
    {
        // $this->addMediaCollection('thumb')->useDisk('public')->acceptsMimeTypes(['image/jpeg','image/jpg','image/png','image/webp'])->withResponsiveImages();
        $this
            ->addMediaCollection('image')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg','image/jpg','image/png','image/webp'])
            ->withResponsiveImages();
    }

}
