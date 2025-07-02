<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cmgmyr\Messenger\Traits\Messagable;

class User extends Authenticatable
{
    use Messagable, HasApiTokens, HasFactory, Notifiable , HasRoles,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'deal_id');
    }

    public function mainInventories()
    {
        return $this->hasMany(MainInventory::class, 'deal_id');
    }


    public static function group_byName()
    {
        $group_permissions = DB::table('permissions')
            ->select('group_name as name')
            ->groupBy('group_name')->get();

        return $group_permissions;
    }

    public static function getpermissionByGroupName($groupName)
    {
        $group_permissions = DB::table('permissions')
            ->where('group_name',$groupName)->get();

        return $group_permissions;
    }

    public static function roleHasPermission($role,$permissions)
    {
        $hasPermission = true;

        foreach ($permissions as $permission)
        {
            if (!$role->hasPermissionTo($permission->name) )
            {
                return $hasPermission = false;
            }

            return $hasPermission;
        }

    }

    public function hasAllaccess()
    {
        return $this->hasRole('admin');

    }



    // public function role()
    // {
    //     $this->belongsTo(Role::class, 'role_id');
    // }


    public function getFormattedPhoneNumberAttribute()
    {
        return $this->formatPhoneNumber($this->phone);
    }

    private function formatPhoneNumber($phoneNumber)
    {
        $formatted = preg_replace("/(\d{3})(\d{3})(\d{4})/", "($1) $2-$3", $phoneNumber);
        return $formatted;
    }


    public function getIsAdminAttribute()
    {
        return $this->role === 'admin';
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class,'membership_id','id');
    }



}
