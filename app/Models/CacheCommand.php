<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CacheCommand extends Model
{
    use HasFactory;
    protected $fillable =['id', 'name', 'command', 'city', 'state', 'zip_codes', 'county', 'cache_file', 'status'];
}
