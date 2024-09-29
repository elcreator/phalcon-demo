<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

class Acl extends \Phalcon\Acl\Adapter\Memory
{
    const ROLES = 'roles';
    const ROLE_GUEST = 'guest';
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    /** @var Phalcon\Di\Di */
    private $_di;
    /** @var array */
    private $_systemRoles;
    /** @var array */
    private $_myRoles;
    /** @var bool */
    private $_isGuest;

    /**
     * @param $di
     */
    public function __construct($di)
    {
        $this->_di = $di;
        $this->_systemRoles = [
            self::ROLE_GUEST => 'Can browse site',
            self::ROLE_USER => 'Can access content for authorized users',
            self::ROLE_ADMIN => 'Can do anything'
        ];
        $this->_fill();
        $this->_myRoles = [];
    }

    public function isGuest()
    {
        return $this->_isGuest;
    }

    public function setIsGuest($isGuest)
    {
        $this->_isGuest = $isGuest;
    }

    public function setMyRoles(array $roles)
    {
        $this->_myRoles = $roles;
    }

    public function getMyRoles()
    {
        return $this->_myRoles;
    }

    public function getMyRole()
    {
        $myRole = self::ROLE_GUEST;
        foreach ($this->_systemRoles as $systemRole => $description) {
            if (in_array($systemRole, $this->_myRoles)) {
                $myRole = $systemRole;
            }
        }
        return $myRole;
    }

//    private function _unwrapWildcards($wildcardResources)
//    {
//        $resourceActions = $this->_di->getShared('config')->get('resources')->toArray();
//        $resources = [];
//        foreach ($wildcardResources as $resourceName => $wildcardPermissions) {
//            $permissions = [];
//            foreach ($wildcardPermissions as $wildcardRoleName => $wildcardActions) {
//                $actions = ($wildcardActions === '*') ? $resourceActions[$resourceName] : $wildcardActions;
//                if ($wildcardRoleName === '*') {
//                    $roleNames = array_keys($this->_systemRoles);
//                    foreach ($roleNames as $roleName) {
//                        $permissions[$roleName] = array_key_exists($roleName, $permissions)
//                            ? array_merge($permissions[$roleName], $actions)
//                            : $actions;
//                    }
//                } else {
//                    $permissions[$wildcardRoleName] = array_key_exists($wildcardRoleName, $permissions)
//                        ? array_merge($permissions[$wildcardRoleName], $actions)
//                        : $actions;
//                }
//            }
//            $resources[$resourceName] = $permissions;
//        }
//        return $resources;
//    }

    private function _fill()
    {
        $this->setDefaultAction(\Phalcon\Acl\Enum::DENY);
        $this->addComponent('content.static-page', ['index', 'notFound']);
        foreach ($this->_systemRoles as $roleName => $roleDescription) {
            $this->addRole(new \Phalcon\Acl\Role($roleName, $roleDescription));
            $this->allow($roleName, 'content.static-page', ['index', 'notFound']);
        }
//        foreach ($this->_systemResources as $resourceName => $permissions) {
//            $this->addComponent(new Phalcon\Acl\Resource($resourceName), $this->_getResourceActions($permissions));
//            $this->_addPermissionsToResource($resourceName, $permissions);
//        }
    }

    /**
     * @param string $resourceName
     * @param array $permissions
     */
//    private function _addPermissionsToResource($resourceName, array $permissions)
//    {
//        foreach ($permissions as $roleName => $actions) {
//            foreach ($actions as $item) {
//                $this->allow($roleName, $resourceName, $item);
//            }
//        }
//    }

    /**
     * @param array $permissions
     * @return array
     */
//    private function _getResourceActions(array $permissions)
//    {
//        $resourceActions = [];
//        foreach ($permissions as $actions) {
//            $resourceActions = array_merge($resourceActions, array_values($actions));
//        }
//        return $resourceActions;
//    }
}
