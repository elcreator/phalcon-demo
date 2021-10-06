<?php
/**
 * @file    Module.php
 * @brief
 * @author  Artur Kirilyuk (artur.kirilyuk@gmail.com)
 * @package Auth
 */

namespace Admin;

class Module extends \BaseModule
{
    public array $externalNamespaces = [
        'Auth\Models' => __DIR__ . '/../auth/models/',
        'Merchant\Models' => __DIR__ . '/../merchant/models/',
        'Profile\Models' => __DIR__ . '/../profile/models/',
    ];
}
