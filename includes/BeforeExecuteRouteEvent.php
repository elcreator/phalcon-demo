<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

class BeforeExecuteRouteEvent
{
    /** @var Phalcon\Logger\Adapter */
    private $_log;

    /**
     * @param \Phalcon\Di\DiInterface $di
     * @param \Phalcon\Mvc\Dispatcher $dispatcher
     */
    public function __construct(\Phalcon\Di\DiInterface $di, \Phalcon\Mvc\Dispatcher $dispatcher)
    {
        $this->_log = $di->get('log');
        $ip = $di->get('request')->getClientAddress();
        /** @var \Acl $acl */
        $acl = $di->get('acl');
        /** @var \I18n $i18n */
        $i18n = $di->get('i18n');
        /** @var \Phalcon\Session\ManagerInterface $session */
        $session = $di->get('session');
        /** @var \Phalcon\Http\Response\Cookies $cookies */
        $cookies = $di->get('cookies');
        if ($cookies->has(SESSION_COOKIE_NAME) && !$session->exists()) {
            $session->start();
        }
        if (!$session->exists()) {
            $acl->setIsGuest(true);
            $_SESSION = []; // workaround for Phalcon fatal errors
        } else {
            $acl->setIsGuest(false);
        }
        if (!$acl->isGuest()) {
            if (!$session->has('lang')) {
                $session->set('lang', $i18n->detectLanguageCode());
            }
            $i18n->setLanguageCode($session->get('lang'));
        } else {
            $i18n->setLanguageCode($i18n->detectLanguageCode());
        }
        $roles = $acl->isGuest() || !$session->exists() ? [] : $this->_getSessionRoles($session, $acl);
        if (empty($roles)) {
            $roles = [$acl::ROLE_GUEST];
        }
        $acl->setMyRoles($roles);
        $module = $dispatcher->getModuleName();
        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();
        $this->_validateAccess($acl, "$module.$controller", $action, $ip);
    }

    /**
     * @param \Phalcon\Acl\AdapterInterface $acl
     * @param string $resource
     * @param string $action
     * @param string $ip
     * @throws \Phalcon\Acl\Exception
     */
    private function _validateAccess(\Acl $acl, $resource, $action, $ip)
    {
        $roles = $acl->getMyRoles();
        foreach ($roles as $role)
        {
            if (!$acl->isAllowed($role, $resource, $action))
            {
                if ($role === $acl::ROLE_GUEST) {
                    throw new \AuthException("Access to $resource $action is denied for guest from $ip!");
                }
            } else {
                return;
            }
        }
        throw new \Phalcon\Acl\Exception("Access to $resource is denied for " . implode(',', $roles) . " from $ip!");
    }

    /**
     * @param \Phalcon\Session\ManagerInterface $session
     * @param Acl $acl
     * @return array
     */
    private function _getSessionRoles($session, \Acl $acl)
    {
        $serializedRoles = $session->get($acl::ROLES);
        $roles = [];
        if (!empty($serializedRoles)) {
            $roles = unserialize($serializedRoles);
        }
        return $roles;
    }
}
