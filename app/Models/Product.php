<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'sku',
        'flavour_id',
        'packing_id',
        'category_id',
        'image',
        'barcode',
        'weight',
        'volume',
        'gst',
        'reorder_level',
        'max_stock_level',
        'status',
    ];
    protected $dates = ['deleted_at'];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
    public function flavour()
    {
        return $this->belongsTo(Flavour::class);
    }
    public function packing()
    {
        return $this->belongsTo(Packing::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    // GST amount is calculated per-item in purchase/sale flows using the actual price
    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
