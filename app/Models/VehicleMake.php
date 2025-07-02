<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleMake extends Model
{
    use HasFactory;
    protected $table = 'vehicle_makes';
    protected $fillable = ['make_name','status','is_read'];

    public function models()
    {
        return $this->hasMany(VehicleModel::class,'vehicle_make_id');
    }
}
