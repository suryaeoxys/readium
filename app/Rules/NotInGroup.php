<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\GroupMember;

class NotInGroup implements Rule
{
    protected $groupId;

    public function __construct($groupId)
    {
        $this->groupId = $groupId;
    }

    public function passes($attribute, $value)
    {
        $members = explode(',', $value);

        foreach ($members as $memberId) {
            if (GroupMember::where('group_id', $this->groupId)
                            ->where('user_id', $memberId)
                            ->exists()) {
                return false; 
            }
        }

        return true; 
    }

    public function message()
    {
        return 'One or more members are already in the group.';
    }
}
