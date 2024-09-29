<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

namespace Auth\Controllers;

use Auth\Models;

abstract class BaseAuthController extends \BaseController
{
    const TOKEN_LENGTH = 50;
    const TOKEN_LIFE_TIME = 31536000; // 365 * 24 * 60 * 60
    const COOKIE_NAME_TOKEN = 'token';

    /**
     * @param Models\User $user
     * @param bool $rememberMe
     */
    protected function _setToken($user, $rememberMe = false)
    {
        $token = $this->security->getSaltBytes(self::TOKEN_LENGTH);
        $user->token = $token;
        $this->cookies->set(self::COOKIE_NAME_TOKEN, $token, $rememberMe ? (time() + self::TOKEN_LIFE_TIME) : 0);
    }

    protected function _setDefaultRightsOnRegister(Models\User $user) {
        $user->isTest = false;
        $user->isBanned = false;
    }

    /**
     * @param Models\User $user
     */
    protected function _processAuthenticated(Models\User $user)
    {
        $roles = $user->getRoles();
        if (!$this->session->exists()) {
            $this->session->start();
        }
        $this->session->set(Models\User::SESSION_ROLES, serialize($roles));
        $this->session->set(Models\EmailUser::SESSION_ID, $user->id);
        if ($user->email === $this->config->get('admin_email'))
        {
            $roles[] = Models\EmailUser::ROLE_ADMIN;
            $this->session->set(Models\EmailUser::SESSION_ROLES, serialize($roles));
            $this->response->redirect('/admin');
        }
        else
        {
            $this->response->redirect('/profile');
        }
        $this->view->disable();
    }

    protected function _getPasswordReset()
    {
        return base64_encode($this->security->getSaltBytes(10));
    }

    /**
     * @param string $password
     * @return string
     */
    protected function _getPasswordHash($password)
    {
        return $this->security->hash($password);
    }
}
