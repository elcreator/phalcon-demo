<?php
/**
 * @file    RegisterController.php
 * @brief
 * @author  Artur Kirilyuk (artur.kirilyuk@gmail.com)
 * @package Auth\Controllers
 */

namespace Auth\Controllers;

use Auth\Models\EmailUser;
use Phalcon\Http\Client\Exception;

class RegisterController extends BaseAuthController
{
    public function indexAction()
    {
        $email = $this->request->getPost('email', 'email', null, true);
        $fullname = $this->request->getPost('fullname', 'string', 'Anonymous', true);
        $password = $this->request->getPost('password', 'string', null, true);

        if (EmailUser::findFirst(['email = ?0', 'bind' => [$email]]))
        {
            $this->log->notice('Duplicate email registration ' . $email);
            $this->view->pick($this->_localizePath('content/auth'));
            $this->flash->error(__('User with provided email already exists!'));
            return;
        }

        $user = new EmailUser();
        $user->email = $email;
        $user->fullname = $fullname;
        $user->password = $this->_getPasswordHash($password);
        $this->_setToken($user);
        $this->_setDefaultRightsOnRegister($user);
        $result = $user->create();
        if (!$result)
        {
            $this->log->notice('Error during registration ' . $email);
            $this->view->pick($this->_localizePath('content/auth'));
            $this->flash->error(__('Cannot register. Please try again.'));
            return;
        }

        $this->_processAuthenticated($user);
    }
}
