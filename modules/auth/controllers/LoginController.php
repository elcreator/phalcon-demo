<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

namespace Auth\Controllers;

use Auth\Models\EmailUser;

class LoginController extends BaseAuthController
{
    public function loginAction()
    {
        $login = $this->request->getPost('login', 'email', null, true);
        $password = $this->request->getPost('password', 'string', null, true);
        $rememberMe = (bool)$this->request->getPost('remember', 'string');
        $user = EmailUser::findFirst([
            'email = ?0',
            'bind' => [$login]
        ]);
        if (!$user || !$this->security->checkHash($password, $user->password))
        {
            $this->log->notice('Unsuccessful login ' . $login . ':' . $password);
            $this->view->pick($this->_localizePath('content/auth'));
            $this->flash->error(__('Wrong password!'));
            return;
        }
        $this->log->info('Successful login ' . $login);
        $this->_setToken($user, $rememberMe);

        $this->_processAuthenticated($user);
    }
}
