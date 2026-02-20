<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LedgerEntry extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'ledgerable_id',
        'ledgerable_type',
        'transaction_id',
        'customer_id',
        'vendor_id',
        'date',
        'description',
        'debit',
        'credit',
        'balance',
        'user_id',  
    ];
    protected $dates = ['date','deleted_at'];

    public function ledgerable()
    {
        return $this->morphTo();
    }
    /**
     * Get the transaction associated with the ledger entry.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
