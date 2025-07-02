<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoldInventories extends Model
{
    use HasFactory;
    protected $table = 'sold_inventories';
    protected $fillable = ['img_exist'];

}
