<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Recommandation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'askrecommandation_id',
        'title',
        'review',
        'link',
        'category_id',
        'image',
        'is_comment',
        'status'
    ];
    protected $appends = [ 'favourite','like', 'editable', 'edit'];

    public function getFavouriteAttribute() {
        
        $user = Auth::guard('api')->user();
        if(!$user) {
            return false;
        }
        $data = Bookmark ::where('user_id', $user->id)->where('post_id', $this->id)->first();
        return !empty($data) ? true : false;
    }

    public function getLikeAttribute() {
        
        $user = Auth::guard('api')->user();
        if(!$user) {
            return false;
        }
        $data = Likes ::where('user_id', $user->id)->where('recommended_id', $this->id)->first();
        return !empty($data) ? true : false;
    }

    public function getEditableAttribute() {
        
        $user = Auth::guard('api')->user();
        if(!$user) {
            return false;
        }
        $data = Recommandation ::where('user_id', $user->id)->where('id', $this->id)->first();
        return !empty($data) ? true : false;
    }
    public function getEditable() {
        
        $user = Auth::guard('api')->user();
        if(!$user) {
            return false;
        }
        $data = Recommandation ::where('user_id', $user->id)->where('id', $this->id)->first();
        return !empty($data) ? true : false;
    }

    public function getEditAttribute() {
        
        $user = Auth::guard('api')->user();
        if(!$user) {
            return false;
        }
        $data = Recommandation ::where('user_id', $user->id)->where('askrecommandation_id', $this->askrecommandation_id)->first();
        return !empty($data) ? true : false;
    }

    public function category(){
        return $this->hasMany(Category::class, 'id', 'category_id');
    }

    public function postUser() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function postCategory(){
        return $this->hasOne(Category::class, 'id', 'category_id')->select('id','name','image');
    }

    public function store() {
        return $this->hasOne(VendorProfile::class, 'id', 'store_id');
    }

    public static function getVariantByProductAndCategoryId($vendorID, $productId, $CategoryID) {
        return static::where('vendor_id', $vendorID)->where('category_id', $CategoryID)->first();
    }

    public static function getDataByUserAndStoreId($userId, $store_id) {
        return static::where('user_id', $userId)->where('store_id', $store_id)->first();
    }

    public static function getIdByUserId($userId) {
        return static::where('user_id', $userId)->pluck('store_id');
    }

    public static function getActiveVendorProductDetailsByID($id) {
        $data = static::where(function ($query_new) use ($id) {
                        $query_new->where('id', $id);
        })->first();
        return $data;
    }

    public static function getRecommandationn($keyword = null){
        return static::where('title', 'like', '%'.$keyword.'%')->orWhere('review','like', '%'.$keyword.'%')->paginate();
    }

    // public static function getRecommandationData(){
    //     // return static::latest()->get();
    //     return static::orderBy('updated_at','desc')->get();
    // }
    public static function getRecommandationData()
    {
        $user = Auth::guard('api')->user();

        // Retrieve blocked user IDs
        $blockedUserIds = BlockUser::where('user_id', $user->id)->pluck('block_user_id')->toArray();

        // Add a condition to exclude blocked users
        return self::whereNotIn('user_id', $blockedUserIds)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function getRecommandationById($recommandationId){
        return static::where('id',$recommandationId)->first();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id')->where('type', 'recommandation');
    }

    public function categoryRecommandation()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // public function postCategory(){
    //     return $this->hasOne(Category::class, 'id', 'category_id');
    // }
    
}