<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CSVTmpLocation extends Model
{
    use HasFactory;
    protected $table = 'csv_location_zips';
    protected $fillable = [
        'short_name',
        'state',
        'city',
        'latitude',
        'longitude',
        'combine_tax',
        'import_status',
    ];
}
