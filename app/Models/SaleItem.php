<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleItem extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'batch_no',
        'location_id',
        'purchase_price',
        'sale_price',
        'total_amount',
    ];
    protected $dates = ['deleted_at'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    public function inventoryTransactions()
    {
        return $this->morphMany(InventoryTransaction::class, 'transactionable');
    }
}
