<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;

class UsersExist implements Rule
{
    public function passes($attribute, $value)
    {
        $userIds = explode(',', $value);
        
        foreach ($userIds as $userId) {
            if (!User::where('id', $userId)->exists()) {
                return false; 
            }
        }

        return true; 
    }

    public function message()
    {
        return 'One or more users specified do not exist.';
    }
}
