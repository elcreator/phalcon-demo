<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

namespace Auth;

class Routes extends \BaseRoutes
{
    protected $_prefix = '/auth';

    public function __construct(\Phalcon\Mvc\Router $router, $module)
    {
        parent::__construct($router, $module);
        $this->post('/register',                          'register')(['*']);
        $this->post('/logout',                            'logout')(['*']);
        $this->post('/login',                             'login',    'login')(['*']);
    }
}
