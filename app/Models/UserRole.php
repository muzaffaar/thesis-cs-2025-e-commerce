<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserRole extends Model
{
    public $table = 'user_roles';

    protected $fillable = [
        'name',
    ];

    public function users() : belongsToMany
    {
        return $this->belongsToMany(User::class, 'user_user_role');
    }
}
