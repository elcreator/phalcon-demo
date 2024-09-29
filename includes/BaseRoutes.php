<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

class BaseRoutes
{
    /** @var string */
    public $prefix = '';
    /** @var Router */
    private $_router;
    /** @var string */
    private $_module;
    /** @var array */
    private $_resourceRoleMap = [];

    public function getResourceRoleMap()
    {
        return $this->_resourceRoleMap;
    }

    /**
     * @param string $url
     * @param string|int|null $controller
     * @param string|int|null $action
     * @param string|int|null $params
     * @param array $custom
     * @return callable
     */
    public function all($url, $controller = null, $action = null, $params = null, $custom = [])
    {
        $this->_router->add("{$this->prefix}$url", $this->_router::makeRouteParams($this->_module, $controller, $action, $params, $custom));
        return $this->_allowedRoles($controller, $action);
    }

    /**
     * @param string $url
     * @param string|int|null $controller
     * @param string|int|null $action
     * @param string|int|null $params
     * @param array $custom
     * @return callable
     */
    public function get($url, $controller = null, $action = null, $params = null, $custom = [])
    {
        $this->_router->addGet("{$this->prefix}$url", $this->_router::makeRouteParams($this->_module, $controller, $action, $params, $custom));
        return $this->_allowedRoles($controller, $action);
    }

    /**
     * @param string $url
     * @param string|int|null $controller
     * @param string|int|null $action
     * @param string|int|null $params
     * @param array $custom
     * @return callable
     */
    public function getStatic($url, $controller = null, $action = null, $params = null)
    {
        $this->_router->addGetStatic("{$this->prefix}$url", $this->_module, $controller, $action, $params);
        return $this->_allowedRoles($controller, $action);
    }

    /**
     * @param string $url
     * @param string|int|null $controller
     * @param string|int|null $action
     * @param string|int|null $params
     * @param array $custom
     * @return callable
     */
    public function post($url, $controller = null, $action = null, $params = null, $custom = [])
    {
        $this->_router->addPost("{$this->prefix}$url", $this->_router::makeRouteParams($this->_module, $controller, $action, $params, $custom));
        return $this->_allowedRoles($controller, $action);
    }


    /**
     * @param string $url
     * @param string|int|null $controller
     * @param string|int|null $action
     * @param string|int|null $params
     * @param array $custom
     * @return callable
     */
    public function put($url, $controller = null, $action = null, $params = null, $custom = [])
    {
        $this->_router->addPut("{$this->prefix}$url", $this->_router::makeRouteParams($this->_module, $controller, $action, $params, $custom));
        return $this->_allowedRoles($controller, $action);
    }

    /**
     * @param string $url
     * @param string|int|null $controller
     * @param string|int|null $action
     * @param string|int|null $params
     * @param array $custom
     * @return callable
     */
    public function delete($url, $controller = null, $action = null, $params = null, $custom = [])
    {
        $this->_router->addDelete("{$this->prefix}$url", $this->_router::makeRouteParams($this->_module, $controller, $action, $params, $custom));
        return $this->_allowedRoles($controller, $action);
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

    protected function _allowedRoles($controller, $action)
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
