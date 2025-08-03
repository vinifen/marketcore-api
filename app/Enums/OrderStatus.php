<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'PENDING';
    case PROCESSING = 'PROCESSING';
    case SHIPPED = 'SHIPPED';
    case COMPLETED = 'COMPLETED';
    case CANCELED = 'CANCELED';
}