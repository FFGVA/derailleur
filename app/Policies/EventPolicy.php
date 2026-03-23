<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Event $event): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Event $event): bool
    {
        return $user->isAdmin() || ($user->isChefPeloton() && $user->member_id && $event->chefs->contains('id', $user->member_id));
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Event $event): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Event $event): bool
    {
        return $user->isAdmin();
    }
}
