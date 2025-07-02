<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CsvTmpDealer extends Model
{
    use HasFactory;
    protected $table = "csv_tmp_dealers";
    protected $fillable = ['name','full_address','address','city','state','zip_code','phone','dealer_homepage'];
}
