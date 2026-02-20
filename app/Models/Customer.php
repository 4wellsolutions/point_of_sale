<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Customer extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'whatsapp',
        'type_id',
        'image',
    ];
    protected $dates = ['deleted_at'];

    public function type()
    {
        return $this->belongsTo(Type::class);
    }
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
    public function ledgerEntries()
    {
        return $this->morphMany(LedgerEntry::class, 'ledgerable');
    }
}
