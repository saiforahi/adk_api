<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\FileAdder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Category
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property boolean $featured
 * @property boolean $digital
 */
class Category extends Model implements HasMedia
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'meta_title',
        'meta_description',
        'icon',
        'banner',
        'slug',
        'commission_rate',
        'featured',
        'digital'
    ];

    protected $casts = [
        'featured' => 'boolean',
        'digital' => 'boolean'
    ];

    /**
     * @return HasMany
     */
    public function sub_category(): HasMany
    {
        return $this->hasMany(SubCategory::class);
    }

    public function media(): MorphMany
    {
        // TODO: Implement media() method.
        return $this->morphMany(Media::class, 'model');
    }


    public function registerAllMediaConversions(): void
    {
        // TODO: Implement registerAllMediaConversions() method.
    }
}
