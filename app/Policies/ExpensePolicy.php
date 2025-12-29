<?php

namespace App\Policies;

use App\Models\User;

class ExpensePolicy extends Policies
{
    public function create(User $user): bool
    {
        return true;
    }

    public function delete(User $user, $expense): bool
    {
        return true;
    }
}
