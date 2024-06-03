<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'author_id',
        'publisher_id',
        'name',
        'slug',
        'main_image',
        'status',
        'no_of_page',
        'isbn',
        'original_title',
        'year_of_publication',
        'pdf_mp3',
        'discription',
    ];

    public function Category() {
        return $this->hasOne(Category::class, 'id','category_id');
    }

    public static function getProductsByCategoryId($category_id = null) {
        return static::where('category_id', $category_id)->get();
    }


    public static function getAllActiveProduct($keyword = null) {
        $data = static::where(function ($query_new) use ($keyword) {
                        $query_new->where('name', 'like', '%'.$keyword.'%')
                        ->where('status',1);
        })->get();
        return $data;
    }

    public static function getActiveProductDetailsByID($id = null) {
        // return static::where('id', $id)->with(['Category.tax','tax'])->first();
        $data = static::where(function ($query_new) use ($id) {
                        $query_new->where('id', $id)
                        ->where('status',1);
        })->first();
        return $data;
    }

    public function subCategories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function author(){
        return $this->belongsTo(Author::class);
    }
    public function publisher(){
        return $this->belongsTo(Publisher::class);
    }
}
