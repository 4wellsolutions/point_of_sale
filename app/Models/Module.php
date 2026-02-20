<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Module extends Model
{
    protected $fillable = ['name', 'label', 'icon', 'order'];

    /**
     * Roles that have access to this module.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_module');
    }
}
