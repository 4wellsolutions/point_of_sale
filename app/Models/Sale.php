<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Sale extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;
    protected $fillable = [
        'customer_id',
        'invoice_no',
        'sale_date',
        'total_amount',
        'discount_amount',
        'net_amount',
        'user_id',
        'notes',

    ];
    protected $dates = ['deleted_at'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function salesReturns()
    {
        return $this->hasMany(SalesReturn::class);
    }
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }
}
