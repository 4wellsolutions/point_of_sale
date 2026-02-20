<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['method_name'];
    protected $dates = ['deleted_at'];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
