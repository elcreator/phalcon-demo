<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

abstract class BaseModule
{
    public array $externalNamespaces = [];

    /**
     * Registers an autoloader related to the module
     *
     * @param DiFactory
     */
    public function registerAutoloaders(\Phalcon\Di\DiInterface $dependencyInjector)
    {
        $loader = new \Phalcon\Autoload\Loader();
        $loader->setNamespaces($this->_getNamespaces())->register();
    }

    /**
     * Registers an autoloader related to the module
     *
     * @param $dependencyInjector
     */
    public function registerServices($dependencyInjector)
    {
    }

    /**
     * @return array
     */
    protected function _getNamespaces() {
        $chunks = explode('\\', get_called_class());
        $className = array_pop($chunks);
        $namespace = implode('\\', $chunks);
        $dir = MODULES_DIR . implode('/', $chunks);
        $defaultNamespaces = [
            $namespace . '\Controllers' => $dir . '/Controllers',
            $namespace . '\Models' => $dir . '/Models',
            'Models' => BASE_PATH . 'Models/'
        ];
        return array_merge($defaultNamespaces, $this->externalNamespaces);
    }
}
