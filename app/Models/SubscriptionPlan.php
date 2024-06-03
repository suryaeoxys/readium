<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;
     
    
    // protected $table = 'subscriptionplans';
     
    protected $fillable = [
        'title' => 'title',
        'sub_title' => 'sub_title',
        'amount' => 'amount',
        'features' => 'features',
        'description' => 'description',
    ];
    


    
}