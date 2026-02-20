<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;
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
