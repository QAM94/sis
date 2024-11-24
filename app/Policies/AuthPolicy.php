<?php

namespace App\Policies;

use App\Models\User;

class AuthPolicy
{
    protected $modelClass;
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        $path = explode('/', request()->path());

        // Validate that the admin path exists and contains a resource
        if (isset($path[1]) && $path[0] === 'admin' && !in_array($path[1], ['login'])) {
            $this->modelClass = str($path[1])->singular()->lower();
        } else {
            $this->modelClass = null; // Fallback if no valid model is resolved
        }
    }

    public function viewAny(User $user): bool
    {
        if(empty($this->modelClass)) {
            return true;
        }
        $permission = $this->modelClass . '-read';
        return $user->hasPermissionTo($permission);
    }

    public function view(User $user): bool
    {
        if(empty($this->modelClass)) {
            return true;
        }
        $permission = $this->modelClass . '-read';
        return $user->hasPermissionTo($permission);
    }

    public function create(User $user): bool
    {
        if(empty($this->modelClass)) {
            return true;
        }
        $permission = $this->modelClass . '-create';
        return $user->hasPermissionTo($permission);
    }

    public function update(User $user): bool
    {
        if(empty($this->modelClass)) {
            return true;
        }
        $permission = $this->modelClass . '-update';
        return $user->hasPermissionTo($permission);
    }

    public function delete(User $user): bool
    {
        if(empty($this->modelClass)) {
            return true;
        }
        $permission = $this->modelClass . '-delete';
        return $user->hasPermissionTo($permission);
    }
}
