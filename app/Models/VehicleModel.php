<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleModel extends Model
{
    use HasFactory;
    protected $table = 'vehicle_models';

    public function makeData()
    {
        return $this->belongsTo(VehicleMake::class,'vehicle_make_id','id');
    }
}
