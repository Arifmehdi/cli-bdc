<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestInventory extends Model
{
    use HasFactory;
    public function getPriceFormateAttribute()
    {
        $price = $this->price != 0 ? '$'.number_format($this->price, 0, '.', ',') : 'Email for price';
        return $price;
    }
}
