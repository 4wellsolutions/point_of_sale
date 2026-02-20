<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;
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
