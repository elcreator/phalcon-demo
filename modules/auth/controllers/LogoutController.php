<?php
/**
 * @file    LogoutController.php
 * @brief
 * @author  Artur Kirilyuk (artur.kirilyuk@gmail.com)
 * @package Auth\Controllers
 */

namespace Auth\Controllers;

class LogoutController extends BaseAuthController
{
    public function indexAction()
    {
        $this->cookies->set(TOKEN_COOKIE_NAME, null, -1);
        $this->cookies->set(SESSION_COOKIE_NAME, null, -1);
        $this->cookies->send();
        if ($this->session->exists()) {
            $this->session->destroy();
        }
        $this->response->redirect('/');
        $this->view->disable();
    }
}
