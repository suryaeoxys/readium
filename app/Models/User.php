<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Auth;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'nickname',
        'email',
        'profile_image',
        'otp',
        'otp_created_at',
        'otp_verified_at',
        'password',
        
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
    ];

    protected $appends = [ 'follow'];

    public function getFollowAttribute() {
        
        $user = Auth::guard('api')->user();
        if(!$user) {
            return false;
        }
        $data = Following ::where('user_id', $user->id)->where('following_id', $this->id)->first();
        return !empty($data) ? true : false;
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    
    public function driver() {
        return $this->hasOne(DriverProfile::class,'user_id');
    }
    
    public function vendor() {
        return $this->hasOne(VendorProfile::class, 'user_id');
    }

    public function address() {
        return $this->hasOne(UserAddress::class,'user_id')->orderBy('created_at', 'DESC');
    }

    public static function getAllVendorsNameAndId() {
        return static::where('is_vendor', '=', 1)->pluck('name', 'id');
    }



    /**
     * Get User Data By Phone no.
     * takes parameter phone no.
     * returns user's data
     */
    public static function findByPhone($phone = null) {
        return static::where('phone', $phone)->first();
    }

    public static function findByEmail($email = null) {
        return static::where('email', $email)->first();
    }

    public static function getUserByEmail($email = null) {
        return static::where('email', $email)->first();
    }

    public static function getAllUserByEmail($email = null) {
        return static::where('email', $email)->withTrashed()->first();
    }

    /**
     * Get User Data By Referal Code.
     * takes parameter referal code.
     * returns user's data
     */
    public static function findByReferalCode($referal_code = null) {
        return static::where('referal_code', $referal_code)->first();
    }

    /**
     * Get User Name By Id.
     * takes parameter User id.
     * returns user's name
     */
    public static function getNameById($id) {
        return static::where('id', $id)->first('name');
    }
    public static function getUserById($id) {
        return static::where('id', $id)->first();
    }
     /**
     * Get vendor By Id.
     * takes parameter User id.
     * returns vendor's data
     */

    public static function getVendorByID($id) {
        return static::where('id', $id)->where('is_vendor',1)->first();
    }
     /**
     * Get vendor search result.
     * takes parameter keyword.
     * returns vendor's data(Array)
     */

     public static function searchVendor($keyword) {
        return static::where('name', 'like', '%'.$keyword.'%')->where('is_vendor',1)->get();
    }
    /**
     * Get Vendor's Name and Id .
     * 
     * returns vendors's Name and Id
     */
    public static function getVendorNameAndId() {
        return static::where('is_vendor', '=', 1)->pluck('name', 'id');
    }
    public function seenMessages()
    {
        return $this->belongsToMany(GroupChat::class, 'message_seen', 'user_id', 'message_id');
    }

}
