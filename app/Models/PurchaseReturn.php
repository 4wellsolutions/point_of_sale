<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseReturn extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_id',
        'vendor_id',
        'invoice_no',
        'return_date',
        'total_amount',
        'discount_amount',
        'net_amount',
        'user_id',
        'notes',
    ];
    protected $dates = ['purchase_date', 'return_date'];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
    public function returnItems()
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }
    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }
    public function ledgerEntries()
    {
        return $this->hasMany(LedgerEntry::class, 'ledgerable_id', 'id');
    }
}
