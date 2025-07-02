<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceHistory extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'price_history';
    protected $fillable = ['inventory_id', 'change_date', 'change_amount', 'amount', 'status'];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
