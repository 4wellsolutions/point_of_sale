<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class StockAdjustment extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;
    protected $fillable = [
        'product_id',
        'batch_id',
        'location_id',
        'type',
        'quantity',
        'category',
        'reason',
        'date',
    ];
    protected $dates = ['deleted_at'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
