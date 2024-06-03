<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportPost extends Model
{
    use HasFactory;
    protected $table = 'report_post';

    protected $fillable = [
        'user_id',
        'post_comment_id',
    ];

}
