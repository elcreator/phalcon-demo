<?php
/**
 * Created by PhpStorm.
 * User: Artur
 * Date: 12.05.2017
 * Time: 03:29
 */

namespace Auth;

class Routes extends \BaseRoutes
{
    protected $_prefix = '/auth';

    public function __construct(\Phalcon\Mvc\Router $router, $module)
    {
        parent::__construct($router, $module);
        $this->_post('/register',                          'register')(['*']);
        $this->_post('/logout',                            'logout')(['*']);
        $this->_post('/login',                             'login',    'login')(['*']);
    }
}
