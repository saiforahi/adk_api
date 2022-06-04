<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Category
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property boolean $featured
 * @property boolean $digital
 */
class Category extends Model
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
}
