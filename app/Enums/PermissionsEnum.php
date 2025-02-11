<?php

namespace App\Enums;

enum PermissionsEnum: string
{
    case ApprovedVendors = 'ApprovedVendors';
    case SellProducts = 'SellProducts';
    case BuyProducts = 'BuyProducts';
}
