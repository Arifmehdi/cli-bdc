<?php

namespace App\Models;

use App\Notifications\VerifyAdminEmail;
use Cmgmyr\Messenger\Traits\Messagable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
// use Laravel\Passport\HasApiTokens;

use Spatie\Permission\Traits\HasRoles;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable implements MustVerifyEmail
{
    use Messagable, HasApiTokens, HasFactory, Notifiable , HasRoles, SoftDeletes;

    protected $guard = 'admin';

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

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyAdminEmail);
    }
}
