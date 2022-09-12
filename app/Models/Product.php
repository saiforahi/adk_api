<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property string $name
 * @property int $category_id
 * @property int $sub_category_id
 * @property int $sub_sub_category_id
 */
class Product extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $guarded = [];
    protected $with=["category","brand"];
    protected $casts = [
        'created_at'=>'datetime:Y-m-d h:i:s A',
        'updated_at'=>'datetime:Y-m-d h:i:s A',
        'deleted_at'=>'datetime:Y-m-d h:i:s A'
    ];

    /**
     * @return BelongsTo
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function sub_category(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function sub_sub_category(): BelongsTo
    {
        return $this->belongsTo(SubSubCategory::class, 'sub_sub_category_id', 'id');
    }

    public function added_by()
    {
        return $this->morphTo();
    }

    /**
     * @return HasOne
     */
    public function stock(): HasOne
    {
        return $this->hasOne(ProductStock::class);
    }

    public function registerMediaConversions(Media $media = null): void
    {

    }

    public function registerMediaCollections(): void
    {

    }
}
