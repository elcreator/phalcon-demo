<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

namespace Content;

class Routes extends \BaseRoutes
{
    protected $_prefix = '';

    public function __construct(\Phalcon\Mvc\Router $router, $module)
    {
        parent::__construct($router, $module);
    }
}
