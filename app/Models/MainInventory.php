<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MainInventory extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'main_inventories';
    protected $fillable = ['deal_id','zip_code','latitude','longitude','vehicle_make_id','title','year','make','model','vin','price','price_rating','miles','type','trim','stock','transmission','engine_details','fuel','drive_info','mpg', 'mpg_city','mpg_highway','exterior_color','interior_color','created_date','stock_date_formated','user_id','payment_price','body_formated','is_feature','is_lead_feature','package','payment_date','active_till','featured_till','is_visibility','batch_no','status','inventory_status','image_count'];

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
        return $this->belongsTo(Dealer::class,'deal_id');
    }


    public function admin()
    {
        return $this->belongsTo(Admin::class,'deal_id');
    }

    public function priceHistory()
    {
        return $this->hasMany(PriceHistory::class, 'inventory_id');
    }

    public function mainPriceHistory()
    {
        return $this->hasMany(MainPriceHistory::class, 'main_inventory_id');
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

    public function additionalInventory()
    {
        return $this->hasOne(AdditionalInventory::class, 'main_inventory_id', 'id');
    }

    // public function setZipCodeAttribute($value)
    // {
    //     $this->attributes['zip_code'] = str_pad($value, 5, '0', STR_PAD_LEFT);
    // }

    // public function getZipCodeAttribute($value)
    // {
    //     return str_pad($value, 5, '0', STR_PAD_LEFT);
    // }
}
