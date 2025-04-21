<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'parent_id',
    ];

    /**
     * ความสัมพันธ์กับ Product
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * ความสัมพันธ์กับ Category ที่เป็น Parent
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * ความสัมพันธ์กับ Category ที่เป็น Children
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * รับ Route Key Name
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
