<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Member;
use App\Models\User;

class MemberPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Member $member): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Member $member): bool
    {
        return $user->isAdmin() || $user->isChefPeloton();
    }

    public function delete(User $user, Member $member): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Member $member): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Member $member): bool
    {
        return $user->isAdmin();
    }
}
