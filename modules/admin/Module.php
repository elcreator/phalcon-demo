<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
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
