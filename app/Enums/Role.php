<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Agent = 'agent';
    case GpManager = 'gp_manager';
}
