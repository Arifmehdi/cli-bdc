<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    public function submenus()
    {
        return $this->hasMany(Menu::class, 'parent')->where('status', 1);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function parent_name()
    {
        return $this->belongsTo(Menu::class, 'parent');
    }
}
