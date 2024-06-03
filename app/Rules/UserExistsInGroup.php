<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\GroupMember;

class UserExistsInGroup implements Rule
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function passes($attribute, $value)
    {
        return GroupMember::where(['group_id'=> $value,'user_id'=>$this->userId])
                    ->exists();
    }

    public function message()
    {
        return 'The logged-in user does not exist in the provided group.';
    }
}
