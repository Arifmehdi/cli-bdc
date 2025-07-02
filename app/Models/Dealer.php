<?php

namespace App\Models;

use Cmgmyr\Messenger\Traits\Messagable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Dealer extends Model
{
    use Messagable, HasApiTokens, HasFactory, Notifiable , HasRoles, SoftDeletes;

    protected $fillable = [
        'password',
        'fname',
        'lname',
        'name',
        'dealer_id',
        'state',
        'dealer_full_address',
        'dealer_website',
        'brand_website',
        'rating',
        'review',
        'email',
        'phone',
        'address',
        'city',
        'zip',
        'image',
        'role_id',
        'import_type',
        'batch_no',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function mainInventories()
    {
        return $this->hasMany(MainInventory::class, 'deal_id');
    }
}
