<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

class Router extends \Phalcon\Mvc\Router
{
    public function __construct()
    {
        parent::__construct(false);
        $this->removeExtraSlashes(true);
        $this->notFound($this->makeRouteParams('content', 'static-page', 'notFound'));
        $this->addGet('/([a-z~\-]*)', $this->makeRouteParams('content', 'static-page', null, null, ['page' => 1]));
    }

    public function addGetStatic($pattern, $module, $controller = null, $action = null, $params = null) {
        $pattern = ltrim($pattern, '/');
        $this->addGet("/($pattern~[a-z]{2})", $this->makeRouteParams($module, $controller, $action, $params, ['page' => 1]));
    }

    /**
     * @param string|int $module
     * @param string|int|null $controller
     * @param string|int|null $action
     * @param string|int|null $params
     * @param array $custom
     * @return array
     */
    public static function makeRouteParams($module, $controller = null, $action = null, $params = null, $custom = [])
    {
        $controller = is_null($controller) ? $module : $controller;
        $action = is_null($action) ? 'index' : $action;
        $result = [
            'module' => $module,
            'controller' => $controller,
            'namespace' => ucfirst($module) . '\\Controllers',
            'action' => $action,
            'params' => $params
        ];
        if (!empty($custom))
        {
            $result = array_merge($result, $custom);
        }
        return $result;
    }
}
