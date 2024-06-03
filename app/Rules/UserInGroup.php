<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\GroupMember;

class UserInGroup implements Rule
{
    private $groupId;

    public function __construct($groupId)
    {
        $this->groupId = $groupId;
    }

    public function passes($attribute, $value)
    {
        return GroupMember::where('group_id', $this->groupId)
                          ->where('user_id', $value)
                          ->exists();
    }

    public function message()
    {
        return 'The user does not exist in the specified group.';
    }
}
