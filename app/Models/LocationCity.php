<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocationCity extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'location_cities';
    protected $fillable = ['city_name','latitude','location_state_id','longitude','status','is_read'];

    public function state()
    {
        return $this->belongsTo(LocationState::class, 'location_state_id');
    }

}