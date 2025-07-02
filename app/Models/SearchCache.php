<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchCache extends Model
{
    use HasFactory;
    protected $table = 'search_cache';
    protected $fillable = ['ip','search_key', 'search_results', 'filter_options', 'last_updated_at','count'];
}
