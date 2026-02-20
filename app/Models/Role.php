<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Role extends Model implements AuditableContract
{
    use Auditable;

    protected $fillable = ['name', 'is_admin'];

    protected $casts = [
        'is_admin' => 'boolean',
    ];

    /**
     * Modules assigned to this role.
     */
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'role_module');
    }

    /**
     * Users that have this role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if this role is an admin role.
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Check if this role has access to a given module.
     */
    public function hasModule(string $slug): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->modules()->where('name', $slug)->exists();
    }
}
