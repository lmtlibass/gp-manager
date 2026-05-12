<?php

namespace App\Enums;

/**
 * @deprecated Use UserRoleEnum.
 */
enum Role: string
{
    case Admin = 'admin';
    case Agent = 'agent';
    case GpManager = 'gp_manager';
}
