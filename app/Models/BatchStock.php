<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class BatchStock extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;
    protected $fillable = [
        'batch_id',
        'product_id',
        'location_id',
        'quantity',
        'purchase_price',
        'sale_price',
        'expiry_date',
    ];
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
