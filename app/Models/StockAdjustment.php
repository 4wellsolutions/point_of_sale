<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockAdjustment extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'product_id',
        'batch_id',
        'location_id',
        'type',
        'quantity',
        'category',
        'reason',
        'date',
    ];
    protected $dates = ['deleted_at'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
