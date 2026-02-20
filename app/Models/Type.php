<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Type extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;
    protected $fillable = ['name'];
    protected $dates = ['deleted_at'];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }
}
