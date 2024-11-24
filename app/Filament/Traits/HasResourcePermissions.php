<?php

namespace App\Filament\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasResourcePermissions
{
    /**
     * Check if the user can access the resource.
     */
    public static function canAccess(): bool
    {
        return static::checkPermission('-read');
    }

    /**
     * Check if the user can create the resource.
     */
    public static function canCreate(): bool
    {
        return static::checkPermission('-create');
    }

    /**
     * Check if the user can delete any resource.
     */
    public static function canDeleteAny(): bool
    {
        return static::checkPermission('-delete');
    }

    /**
     * Check if the user can edit any resource.
     */
    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can(static::$module . '-update', $record);
    }

    /**
     * Reusable method to check permissions dynamically.
     */
    protected static function checkPermission(string $operation): bool
    {
        if (!isset(static::$module)) {
            return false; // Default to deny if module is not defined
        }

        return auth()->user()->can(static::$module . $operation);
    }
}
