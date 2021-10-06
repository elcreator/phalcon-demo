<?php
/**
 * Created by PhpStorm.
 * User: Artur
 * Date: 12.05.2017
 * Time: 03:29
 */

namespace Profile;

class Routes extends \BaseRoutes
{
    protected $_prefix = '/profile';

    public function __construct(\Phalcon\Mvc\Router $router, $module)
    {
        parent::__construct($router, $module);
        $this->_get('')(['user']);
        $this->_get('/me', null, 'me')(['*']);
        $this->_post('/search', null, 'search')(['user']);
        $this->_post('/email', null, 'email')(['user']);
        $this->_post('/password', null, 'password')(['user']);
        $this->_post('/fullname', null, 'fullname')(['user']);
    }
}
