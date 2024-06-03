<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'image',
        'parent_id',
        'status',
    ];

    public function Products() {
        return $this->hasMany(Products::class, 'category_id');
    }

    public static function getAllActiveCategoriesNameId() {
        return static::where('status', '=', 1)->pluck('name', 'id');
    }
    public static function getChildCategoryById($id) {
        return static::where(['status' => 1,'parent_id' => $id])->pluck('name', 'id');
    }
    public static function getParentCategory(){
        return static::where(['parent_id'=>null,'status' => 1])->get();
    }

    public static function lastestCategory() {

        return static::orderby('id','desc')->where('status', 1)->take(12)->get();
    }
}
