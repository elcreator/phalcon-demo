<?php
/**
 * @file    BaseModule.php
 * @brief
 * @author  Artur Kirilyuk (artur.kirilyuk@gmail.com)
 */

abstract class BaseModule
{
    public array $externalNamespaces = [];

    /**
     * Registers an autoloader related to the module
     *
     * @param DiFactory
     */
    public function registerAutoloaders(\DiFactory $dependencyInjector)
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
        $dir = MODULES_DIR . strtolower(implode('/', $chunks));
        $defaultNamespaces = [
            $namespace . '\Controllers' => $dir . '/controllers',
            $namespace . '\Models' => $dir . '/models',
            'Models' => BASE_PATH . '/models/'
        ];
        return array_merge($defaultNamespaces, $this->externalNamespaces);
    }
}
