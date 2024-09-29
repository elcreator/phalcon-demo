<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

namespace Admin;

class Routes extends \BaseRoutes
{
    protected $_prefix = '/admin';

    public function __construct(\Phalcon\Mvc\Router $router, $module)
    {
        parent::__construct($router, $module);
        $this->get('')(['admin']);
        $this->post('/cache/translations', 'cache', 'clearTranslation')(['admin']);
        $this->post('/cache/views', 'cache', 'clearView')(['admin']);
        $this->post('/cache/config', 'cache', 'clearConfig')(['admin']);
        $this->get('/users/{id:[0-9]+}', 'user', 'get')(['admin']);
        $this->put('/users/{id:[0-9]+}', 'user', 'update')(['admin']);
        $this->delete('/users/{id:[0-9]+}', 'user', 'delete')(['admin']);
    }
}
