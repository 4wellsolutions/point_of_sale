<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BatchStock extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'batch_id',
        'product_id',
        'location_id',
        'quantity',
        'purchase_price',
        'sale_price',
        'expiry_date',
    ];
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
