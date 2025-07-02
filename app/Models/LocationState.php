<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocationState extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'location_states';
    protected $fillable = ['state_name','short_name','sales_tax','status','is_read','batch_no'];
}
