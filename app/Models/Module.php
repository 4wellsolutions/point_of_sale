<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Module extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;
    protected $fillable = ['name', 'label', 'icon', 'order'];

    /**
     * Roles that have access to this module.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_module');
    }
}
