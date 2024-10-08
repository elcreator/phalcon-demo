<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

namespace Auth\Models;

abstract class User extends \BaseModel
{
    const SESSION_ROLES = 'roles';
    const SESSION_ID = 'id';
    const ROLE_BANNED = 'banned';
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    protected $_int = ['id'];
    protected $_bool = ['is_banned', 'is_test'];
    protected $_string = ['email', 'fullname', 'password', 'token'];

    public ?int $id = null;
    public string $email;
    public string $fullname;
    public ?string $password;
    public string $token;
    /** @var bool */
    public $isBanned;
    /** @var bool */
    public $isTest;

    abstract public function initialize();

    public function getRoles()
    {
        $roles = [];
        if ($this->isBanned) {
            $roles[] = self::ROLE_BANNED;
        } else {
            $roles[] = self::ROLE_USER;
        }
        return $roles;
    }
}
