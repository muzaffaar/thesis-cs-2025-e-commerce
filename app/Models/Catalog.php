<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Catalog extends Model
{
    protected $table = 'catalogs';

    protected $fillable = [
        'name',
        'description',
        'slug',
        'parent_id',
    ];

    public function translations() : hasMany {
        return $this->hasMany(CatalogTranslation::class);
    }

    public function translation($locale = null) : HasOne
    {
        $locale = $locale ?? app()->getLocale();
        return $this->hasOne(CatalogTranslation::class)->where('locale', $locale);
    }

    public function parent() : belongsTo
    {
        return $this->belongsTo(Catalog::class, 'parent_id');
    }

    public function children() : hasMany
    {
        return $this->hasMany(Catalog::class, 'parent_id');
    }

    public function images() : \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
