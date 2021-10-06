<?php
/**
 * @file    Module.php
 * @brief
 * @author  Artur Kirilyuk (artur.kirilyuk@gmail.com)
 * @package Auth
 */

namespace Profile;

class Module extends \BaseModule
{
    public array $externalNamespaces = ['Auth\Models' => __DIR__ . '/../auth/models'];
}
