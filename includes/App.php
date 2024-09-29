<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

class App extends \Phalcon\Mvc\Application
{
    /**
     * @param DiFactory
     */
    public function __construct(DiFactory $dependencyInjector)
    {
        parent::__construct($dependencyInjector);
        $this->setDefaultModule('Content');
        $moduleNames = \Sources::getModuleNames();
        $this->_registerModules($moduleNames);
        $this->_addModuleRoutes($moduleNames, $dependencyInjector->getShared('acl'));
    }

    /**
     * @param array $moduleNames
     */
    private function _registerModules($moduleNames)
    {
        $this->registerModules(\Sources::getRegisterModulesConfig($moduleNames));
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
