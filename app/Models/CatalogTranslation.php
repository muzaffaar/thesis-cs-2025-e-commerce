<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogTranslation extends Model
{
    protected $fillable = ['category_id', 'locale', 'name', 'slug'];

    public function catalog() : belongsTo
    {
        return $this->belongsTo(Catalog::class);
    }
}
