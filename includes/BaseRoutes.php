<?php

/**
 * Created by PhpStorm.
 * User: Artur
 * Date: 12.05.2017
 * Time: 03:31
 */
class BaseRoutes
{
    /** @var Router */
    private $_router;
    /** @var string */
    private $_module;
    /** @var array */
    private $_resourceRoleMap = [];
    /** @var string */
    protected $_prefix = '';

    public function getResourceRoleMap()
    {
        return $this->_resourceRoleMap;
    }

    /**
     * @param \Phalcon\Mvc\Router $router
     * @param string $module
     */
    protected function __construct($router, $module)
    {
        $this->_router = $router;
        $this->_module = $module;
    }

    /**
     * @param string $url
     * @param string|int|null $controller
     * @param string|int|null $action
     * @param string|int|null $params
     * @param array $custom
     * @return callable
     */
    protected function _all($url, $controller = null, $action = null, $params = null, $custom = [])
    {
        $this->_router->add("{$this->_prefix}$url", $this->_router::makeRouteParams($this->_module, $controller, $action, $params, $custom));
        return $this->allowedRoles($controller, $action);
    }

    /**
     * @param string $url
     * @param string|int|null $controller
     * @param string|int|null $action
     * @param string|int|null $params
     * @param array $custom
     * @return callable
     */
    protected function _get($url, $controller = null, $action = null, $params = null, $custom = [])
    {
        $this->_router->addGet("{$this->_prefix}$url", $this->_router::makeRouteParams($this->_module, $controller, $action, $params, $custom));
        return $this->allowedRoles($controller, $action);
    }

    /**
     * @param string $url
     * @param string|int|null $controller
     * @param string|int|null $action
     * @param string|int|null $params
     * @param array $custom
     * @return callable
     */
    protected function _getStatic($url, $controller = null, $action = null, $params = null)
    {
        $this->_router->addGetStatic("{$this->_prefix}$url", $this->_module, $controller, $action, $params);
        return $this->allowedRoles($controller, $action);
    }

    /**
     * @param string $url
     * @param string|int|null $controller
     * @param string|int|null $action
     * @param string|int|null $params
     * @param array $custom
     * @return callable
     */
    protected function _post($url, $controller = null, $action = null, $params = null, $custom = [])
    {
        $this->_router->addPost("{$this->_prefix}$url", $this->_router::makeRouteParams($this->_module, $controller, $action, $params, $custom));
        return $this->allowedRoles($controller, $action);
    }


    /**
     * @param string $url
     * @param string|int|null $controller
     * @param string|int|null $action
     * @param string|int|null $params
     * @param array $custom
     * @return callable
     */
    protected function _put($url, $controller = null, $action = null, $params = null, $custom = [])
    {
        $this->_router->addPut("{$this->_prefix}$url", $this->_router::makeRouteParams($this->_module, $controller, $action, $params, $custom));
        return $this->allowedRoles($controller, $action);
    }

    /**
     * @param string $url
     * @param string|int|null $controller
     * @param string|int|null $action
     * @param string|int|null $params
     * @param array $custom
     * @return callable
     */
    protected function _delete($url, $controller = null, $action = null, $params = null, $custom = [])
    {
        $this->_router->addDelete("{$this->_prefix}$url", $this->_router::makeRouteParams($this->_module, $controller, $action, $params, $custom));
        return $this->allowedRoles($controller, $action);
    }

    protected function allowedRoles($controller, $action)
    {
        $self = $this;
        return function (array $roles) use ($self, $controller, $action) {
            $module = $self->_module;
            $controller = is_null($controller) ? $this->_module : $controller;
            $action = is_null($action) ? 'index' : $action;
            $roleMap = empty($self->_resourceRoleMap["$module.$controller"])
                ? []
                : $self->_resourceRoleMap["$module.$controller"];
            foreach ($roles as $role) {
                $roleMap[$role][] = $action;
            }
            $self->_resourceRoleMap["$module.$controller"] = $roleMap;
        };
    }
}
