<?php
/**
 * Created by PhpStorm.
 * User: Artur
 * Date: 12.05.2017
 * Time: 03:29
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
