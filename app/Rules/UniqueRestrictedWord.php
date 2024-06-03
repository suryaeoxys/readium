<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\RestrictedWord;

class UniqueRestrictedWord implements Rule
{
    protected $groupId;

    public function __construct($groupId)
    {
        $this->groupId = $groupId;
    }

    public function passes($attribute, $value)
    {
        return !RestrictedWord::where('group_id', $this->groupId)
                              ->where('word', $value)
                              ->exists();
    }

    public function message()
    {
        return 'The restricted word already exists in this group.';
    }
}
