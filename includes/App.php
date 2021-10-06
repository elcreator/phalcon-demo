<?php
/**
 * Created by PhpStorm.
 * User: Artur
 * Date: 13.05.2017
 * Time: 18:17
 */

/** @property \Phalcon\Logger\Multiple log */
class App extends \Phalcon\Mvc\Application
{
    /**
     * @param DiFactory
     */
    public function __construct(DiFactory $dependencyInjector)
    {
        parent::__construct($dependencyInjector);
        $defaultModules = ['content'];
        $this->setDefaultModule($defaultModules[0]);
        $moduleNames = array_merge($defaultModules, $dependencyInjector->getShared('config')->get('modules')->toArray());
        $this->_registerModules($moduleNames);
        $this->_addModuleRoutes($moduleNames, $dependencyInjector->getShared('acl'));
    }

    /**
     * @param array $moduleNames
     */
    private function _registerModules($moduleNames)
    {
        $this->registerModules($this->_getRegisterModulesConfig($moduleNames));
    }

    /**
     * @param array $moduleNames
     * @return array
     */
    private function _getRegisterModulesConfig($moduleNames)
    {
        $modulesConfig = [];
        foreach ($moduleNames as $moduleName) {
            $modulesConfig[$moduleName] = [
                'className' => ucfirst($moduleName) . '\Module',
                'path' => MODULES_DIR . $moduleName . '/Module.php'
            ];
        }
        return $modulesConfig;
    }

    /**
     * @param array $moduleNames
     * @param \Acl $acl
     */
    private function _addModuleRoutes($moduleNames, $acl)
    {
        $systemRoles = $acl->getRoles();
        $result = [];
        foreach ($moduleNames as $moduleName) {
            $routesFile = MODULES_DIR . $moduleName . '/Routes.php';
            if (!file_exists($routesFile)) {
                continue;
            }
            require $routesFile;
            $routesClass = ucfirst($moduleName) . '\Routes';
            /** @var \BaseRoutes $moduleRoutes */
            $moduleRoutes = new $routesClass($this->router, $moduleName);
            $moduleAcl = $moduleRoutes->getResourceRoleMap();
            $result[] = $moduleAcl;
            foreach ($moduleAcl as $aclResource => $roleMethodMap) {
                $actions = [];
                foreach ($roleMethodMap as $role => $methods) {
                    foreach ($methods as $method) {
                        if (!in_array($method, $actions)) {
                            $actions[] = $method;
                        }
                    }
                }
                $acl->addComponent($aclResource, $actions);
                foreach ($roleMethodMap as $role => $methods) {
                    if ($role !== '*') {
                        $acl->allow($role, $aclResource, $methods);
                    } else {
                        foreach ($systemRoles as $role) {
                            $acl->allow($role, $aclResource, $methods);
                        }
                    }
                }
            }
        }
    }
}
