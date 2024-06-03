<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class RestrictedWord extends Model
{

    public $table = 'restricted_words';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'group_id',
        'word',
    ];
    

}