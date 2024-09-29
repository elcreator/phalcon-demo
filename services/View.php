<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

class TwigRendererEngine extends Phalcon\Mvc\View\Engine\Volt
{
    protected static $s;

    /**
     * @param View        $view
     * @param $container
     */
    public function __construct($view, $container = null)
    {
        $container = $view->container;
        parent::__construct($view, $container);
        $viewConfig = $container->get('config')->get('view')->toArray();
        static::$s = $container->get('security');
        $viewConfig['path'] = VIEWS_CACHE_DIR;
        $this->getCompiler()->setOptions($viewConfig);
        $this->getCompiler()->addFunction('__', '__');
        $this->getCompiler()->addFunction('strtotime', 'strtotime');
        $this->getCompiler()->addFunction('token', self::class . '::token');
    }

    public static function token()
    {
       return '<input type="hidden" name="' . static::$s->getTokenKey()
           . '" value="' . static::$s->getToken() . '" />' . PHP_EOL;
    }
}

class View extends \Phalcon\Mvc\View
{
    public function __construct()
    {
        $this->setViewsDir(VIEWS_DIR);
        $this->registerEngines([
            '.phtml'    => Phalcon\Mvc\View\Engine\Php::class,
            '.twig'     => TwigRendererEngine::class
        ]);
    }
}
