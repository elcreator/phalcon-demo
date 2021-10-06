<?php
/**
 * Created by PhpStorm.
 * User: Artur
 * Date: 12.05.2017
 * Time: 03:29
 */

namespace Admin;

class Routes extends \BaseRoutes
{
    protected $_prefix = '/admin';

    public function __construct(\Phalcon\Mvc\Router $router, $module)
    {
        parent::__construct($router, $module);
        $this->_get('')(['admin']);
        $this->_post('/cache/translations', 'cache', 'clearTranslation')(['admin']);
        $this->_post('/cache/views', 'cache', 'clearView')(['admin']);
        $this->_post('/cache/config', 'cache', 'clearConfig')(['admin']);
        $this->_get('/users/{id:[0-9]+}', 'user', 'get')(['admin']);
        $this->_put('/users/{id:[0-9]+}', 'user', 'update')(['admin']);
        $this->_delete('/users/{id:[0-9]+}', 'user', 'delete')(['admin']);
    }
}
