<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogSubCategory extends Model
{
    use HasFactory;

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'sub_category_id');
    }
}
