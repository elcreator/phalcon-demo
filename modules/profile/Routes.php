<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

namespace Profile;

class Routes extends \BaseRoutes
{
    protected $_prefix = '/profile';

    public function __construct(\Phalcon\Mvc\Router $router, $module)
    {
        parent::__construct($router, $module);
        $this->get('')(['user']);
        $this->get('/me', null, 'me')(['*']);
        $this->post('/search', null, 'search')(['user']);
        $this->post('/email', null, 'email')(['user']);
        $this->post('/password', null, 'password')(['user']);
        $this->post('/fullname', null, 'fullname')(['user']);
    }
}
