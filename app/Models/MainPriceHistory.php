<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MainPriceHistory extends Model
{
    use HasFactory,SoftDeletes;
    protected $table='main_price_history';
    protected $fillable = ['main_inventory_id', 'change_date', 'change_amount', 'amount', 'status'];
}
