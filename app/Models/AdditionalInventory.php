<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalInventory extends Model
{
    use HasFactory;
    protected $table = 'additional_inventories';
    protected $fillable = ['main_inventory_id','detail_url','img_from_url','local_img_url','vehicle_feature_description','vehicle_additional_description','seller_note'];
}
