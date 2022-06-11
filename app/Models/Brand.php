<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string name
 **/
class Brand extends Model
{

    protected $fillable = [
        'name',
        'meta_title',
        'meta_description',
        'logo',
        'slug',
        'top',
        'serial'
    ];
}
