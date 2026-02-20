<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['sale_id', 'payment_method_id', 'amount', 'payment_date'];
    protected $dates = ['deleted_at'];

    // Define the relationship with the PaymentMethod model
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    // Define the relationship with the Sale model
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
