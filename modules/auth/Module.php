<?php
/**
 * @file    Module.php
 * @brief
 * @author  Artur Kirilyuk (artur.kirilyuk@gmail.com)
 * @package Auth
 */

namespace Auth;

class Module extends \BaseModule
{
    public array $externalNamespaces = [
        'Profile\Controllers' => __DIR__ . '/../profile/controllers/',
    ];
}
