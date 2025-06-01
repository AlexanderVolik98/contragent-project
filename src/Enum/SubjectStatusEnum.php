<?php

namespace App\Enum;

enum SubjectStatusEnum: string
{
    case ACTIVE = 'ACTIVE';//
    case INACTIVE = 'INACTIVE';
    case LIQUIDATED = 'LIQUIDATED';//
    case REORGANIZING = 'REORGANIZING';//
    case SUSPENDED = 'SUSPENDED';
    case UNDEFINED = 'UNDEFINED';
    case BANKRUPT = 'BANKRUPT';//
    case LIQUIDATING = 'LIQUIDATING';//
}
