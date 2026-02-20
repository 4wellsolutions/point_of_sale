<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Batch extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;
    protected $fillable = [
        'product_id',
        'batch_no',
        'purchase_date',
        'expiry_date',
        'quantity',
        'invoice_no',
    ];
    protected $dates = ['deleted_at'];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }
    public function stock()
    {
        return $this->hasOne(BatchStock::class);
    }
    public function batchstocks()
    {
        return $this->hasMany(BatchStock::class);
    }

}
