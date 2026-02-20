<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class InventoryTransaction extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;
    protected $fillable = [
        'product_id',
        'batch_id',
        'location_id',
        'quantity',
        'transactionable_type',
        'transactionable_id',
        'user_id',
    ];
    protected $dates = ['deleted_at'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function transactionable()
    {
        return $this->morphTo();
    }
    public function ledgerable()
    {
        return $this->morphTo();
    }
}
