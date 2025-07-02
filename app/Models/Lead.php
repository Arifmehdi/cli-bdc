<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    public function inventory()
    {
    return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function mainInventory()
    {
    return $this->belongsTo(MainInventory::class, 'inventory_id');
    }

    public function customer()
    {
    return $this->belongsTo(User::class, 'user_id');
    }

    public function dealer()
    {
    return $this->belongsTo(User::class, 'dealer_id');
    }
}
