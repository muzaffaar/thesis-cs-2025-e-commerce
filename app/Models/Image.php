<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    protected $table = 'images';

    protected $fillable = ['url', 'alt_text', 'sort_order'];

    public function imageable() : morphTo
    {
        return $this->morphTo();
    }
}
