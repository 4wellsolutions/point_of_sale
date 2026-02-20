<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseReturnItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_return_id',
        'purchase_item_id',
        'product_id',
        'batch_no',
        'quantity',
        'unit_price',
        'total_amount',
    ];

    /**
     * Get the purchase return that owns the item.
     */
    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    /**
     * Get the original purchase item.
     */
    public function purchaseItem()
    {
        return $this->belongsTo(PurchaseItem::class);
    }

    /**
     * Get the product associated with the return item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}