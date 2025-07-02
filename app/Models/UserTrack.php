<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTrack extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['id', 'type', 'title', 'inventory_id', 'user_id', 'links', 'image', 'ip_address', 'count'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
