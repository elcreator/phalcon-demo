<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

class Sources
{
    static public function getResourceActions()
    {
        $resourceActions = [];
        $resourcePaths = glob(MODULES_DIR . '*/Controllers/*Controller.php');
        foreach ($resourcePaths as $path) {
            require $path;
            preg_match('~/([[:alnum:]]+)/Controllers/([[:alnum:]]+)Controller.php~', $path, $matches);
            list($relativePath, $moduleName, $controllerName) = $matches;
            $resourceName = "$moduleName." . strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $controllerName));
            $fullClass = ucfirst($moduleName) . "\\Controllers\\$controllerName" . 'Controller';
            $methods = get_class_methods($fullClass);
            $actions = [];
            foreach ($methods as $method) {
                if (preg_match('~([[:alnum:]]+)Action~', $method, $action)) {
                    $actions[] = $action[1];
                }
            }
            if (empty($actions)) continue;
            $resourceActions[$resourceName] = $actions;
        }
        return $resourceActions;
    }

    static public function getModuleNames()
    {
        return array_diff(scandir(MODULES_DIR), ['.', '..']);
    }

    static public function getRegisterModulesConfig($moduleNames)
    {
        $modulesConfig = [];
        foreach ($moduleNames as $moduleName) {
            $modulesConfig[$moduleName] = [
                'className' => "{$moduleName}\Module",
                'path' => MODULES_DIR . "{$moduleName}/Module.php"
            ];
        }
        return $modulesConfig;
    }
}