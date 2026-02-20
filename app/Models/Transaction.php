<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'payment_method_id', 
        'vendor_id',
        'customer_id',
        'user_id',
        'amount', 
        'transactionable_id', 
        'transactionable_type', 
        'transaction_type', 
        'transaction_date'
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
    ];
    protected $dates = ['deleted_at'];

    // Polymorphic relationship
    public function transactionable()
    {
        return $this->morphTo();
    }

    // Relationship with PaymentMethod
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    // Relationship with Vendor
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // Relationship with Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getFormattedTransactionDateAttribute()
    {
        return $this->transaction_date->format('Y-m-d H:i');
    }
}
