<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
=======
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
>>>>>>> parent of ca0584b... add product stocks crud

class Product extends Model
{
    use HasFactory;
    public function added_by()
    {
        return $this->morphTo();
    }
}
