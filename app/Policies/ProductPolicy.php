<?php

namespace App\Policies;

use App\Models\Products;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Products $product): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Products $product): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Products $product): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Products $product): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Products $product): bool
    {
        return $user->isAdmin();
    }

    public function import(User $user): bool
    {
        return $user->isAdmin();
    }
}
