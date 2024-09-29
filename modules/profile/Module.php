<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

namespace Profile;

class Module extends \BaseModule
{
    public array $externalNamespaces = ['Auth\Models' => __DIR__ . '/../auth/models'];
}
