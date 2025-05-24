<?php

namespace App\Policies;

use App\Models\Catalog;
use App\Models\User;

class CatalogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_catalog');
    }

    public function view(User $user, Catalog $catalog): bool
    {
        return $user->can('view_catalog');
    }

    public function create(User $user): bool
    {
        return $user->can('create_catalog');
    }

    public function update(User $user, Catalog $catalog): bool
    {
        return $user->can('update_catalog');
    }

    public function delete(User $user, Catalog $catalog): bool
    {
        return $user->can('delete_catalog');
    }

    public function restore(User $user, Catalog $catalog): bool
    {
        return $user->can('restore_catalog');
    }

    public function forceDelete(User $user, Catalog $catalog): bool
    {
        return $user->can('force_delete_catalog');
    }
}
