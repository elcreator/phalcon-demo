<?php
/**
 * Created by PhpStorm.
 * User: Artur.Kyryliuk
 * Date: 5/16/16
 * Time: 2:39 PM
 */

class DiFactory extends \Phalcon\DI\FactoryDefault
{
    public function __construct()
    {
        parent::__construct();
        $this->setShared('config', function ()
        {
            if (!file_exists(CONFIG_CACHE_PATH)) {
                $config = new \Phalcon\Config\Adapter\Json(APP_CONFIG_PATH);
                $configArray = $config->toArray();
                $result = '<?php return ' . trim(var_export($configArray, true)) . ';';
                file_put_contents(CONFIG_CACHE_PATH, $result);
            }
            return new \Phalcon\Config\Adapter\Php(CONFIG_CACHE_PATH);
        });
        $this->setShared('log', function ()
        {
            $fileAdapter = new Phalcon\Logger\Adapter\Stream(LOG_PATH);
            $logger = new \Phalcon\Logger(
                'messages', [
                    'local' => $fileAdapter
                ]
            );
            return $logger;
        });
        $this->set('modelsCache', function ()
        {
            $serializerFactory = new \Phalcon\Storage\SerializerFactory();
            $adapterFactory = new \Phalcon\Cache\AdapterFactory($serializerFactory);
            $adapter = $adapterFactory->newInstance('apcu', [
                'defaultSerializer' => 'Php',
                'lifetime' => 5
            ]);
            return new \Phalcon\Cache($adapter);
        });
        $this->setShared('i18n', function ()
        {
            $i18n = new \I18n();
            $i18n->languageCodes = $this->_getFromConfig('languages');
            $i18n->defaultLanguageCode = I18N_DEFAULT_LANG;
            return $i18n;
        });
        $this->setShared('session', function ()
        {
            $session = new \Phalcon\Session\Manager();
            $session->setAdapter(new \Phalcon\Session\Adapter\Stream());
            return $session;
        });
        $this->setShared('router', new \Router);
        $this->setShared('dispatcher', function ()
        {
            $dispatcher = new \Phalcon\Mvc\Dispatcher();
            $dispatcher->setEventsManager(new \EventsManager($this));
            return $dispatcher;
        });
        $this->setShared('crypt', function ()
        {
            $crypt = new \Phalcon\Crypt;
            $crypt->setKey($this->_getFromConfig('crypt', 'key'));
            return $crypt;
        });
        $this->setShared('cookies', function ()
        {
            $cookies = new \Phalcon\Http\Response\Cookies();
            $cookies->useEncryption(false);
            return $cookies;
        });
        $this->setShared('acl', new \Acl($this));
        $this->setShared('view', new \View);
        $this->setShared('flash', function ()
        {
            $flash = new \Flash();
            $flash->setImplicitFlush(false);
            $flash->setAutoescape(false);
            $flash->setMessageInnerTemplate(Flash::DEFAULT_TEMPLATE);
            return $flash;
        });
        $this->setShared('flashSession', function ()
        {
            $flashSession = new \FlashSession();
            $flashSession->setAutoescape(false);
            $flashSession->setMessageInnerTemplate(FlashSession::DEFAULT_TEMPLATE);
            return $flashSession;
        });
        $this->setShared('db', function ()
        {
            return new \Phalcon\Db\Adapter\Pdo\Mysql($this->_getFromConfig('db'));
        });
        $this->setShared('mailer', new \Mailer());
    }

    private function _getFromConfig($section, $key = null)
    {
        $sectionArray = $this->getShared('config')->get($section)->toArray();
        return is_null($key) ? $sectionArray : (array_key_exists($key, $sectionArray) ? $sectionArray[$key] : null);
    }
}
