<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Latlongs extends Model
{
    use HasFactory;
    protected $table='latlongs'; 
    protected $fillable=['zip_code','latitude','longitude'];
}
