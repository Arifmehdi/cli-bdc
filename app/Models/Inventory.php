<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventories';
    protected $fillable = ['dealer_id','deal_id','zip_code','latitude','longitude','detail_url','img_from_url','local_img_url','vehicle_make_id','title','year','make','model','vin','price','price_rating','miles','type','modelNo','trim','stock','engine_details','transmission','body_description','vehicle_feature_description','vehicle_additional_description','fuel','drive_info','mpg', 'mpg_city','mpg_highway','exterior_color','interior_color','star','created_date','stock_date_formated','user_id','payment_price','body_formated','is_feature','status','inventory_status','batch_no','seller_note'];

    public function getPriceFormateAttribute()
    {
        $price = $this->price != 0 ? '$'.number_format($this->price, 0, '.', ',') : 'Email for price';
        return $price;
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function dealer()
    {
        return $this->belongsTo(User::class,'deal_id');
    }
    
    public function priceHistory()
    {
        return $this->hasMany(PriceHistory::class, 'inventory_id');
    }

    public function getFormattedTransmissionAttribute()
    {
        $transmission = strtolower($this->transmission); // Assuming 'transmission' is your column name

        if (strpos($transmission, 'automatic') !== false) {
            return 'Automatic';
        } elseif (strpos($transmission, 'variable') !== false) {
            return 'Variable';
        } else {
            return 'Manual'; // or any default value
        }
    }

}
