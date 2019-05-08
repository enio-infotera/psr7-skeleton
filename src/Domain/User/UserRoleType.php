<?php

namespace App\Domain\User;

use App\Domain\Type\TypeInterface;

/**
 * Type.
 */
final class UserRoleType implements TypeInterface
{
    /**
     * Admin role.
     */
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * User role.
     */
    public const ROLE_USER = 'ROLE_USER';
}
