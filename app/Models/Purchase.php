<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'vendor_id',
        'invoice_no',
        'purchase_date',
        'total_amount',
        'discount_amount',
        'net_amount',
        'user_id',
        'notes',
    ];
    protected $dates = [
        'purchase_date',
        'deleted_at',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
    public function getAvailableStockAttribute()
    {
        $soldQuantity = $this->saleItems()->sum('quantity_sold');
        return $this->qty_received - $soldQuantity;
    }
    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
    // Define the polymorphic relationship to Ledger
    public function ledgerEntries()
    {
        return $this->morphMany(LedgerEntry::class, 'ledgerable');
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
