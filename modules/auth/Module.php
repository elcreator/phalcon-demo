<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

namespace Auth;

class Module extends \BaseModule
{
    public array $externalNamespaces = [
        'Profile\Controllers' => __DIR__ . '/../profile/controllers/',
    ];
}
