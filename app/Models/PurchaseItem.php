<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseItem extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'purchase_id',
        'product_id',
        'location_id',
        'batch_no',
        'expiry_date',
        'quantity',
        'purchase_price',
        'sale_price',
        'total_amount',
    ];
    protected $dates = ['deleted_at'];
    
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    // Relationship to Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    
}
