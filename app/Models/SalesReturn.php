<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class SalesReturn extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;
    protected $fillable = [
        'sale_id',
        'qty_returned',
        'return_reason',
        'refund_amount',
    ];
    protected $dates = ['deleted_at'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
