<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Packing extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['type', 'unit_size', 'description'];
    protected $dates = ['deleted_at'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
