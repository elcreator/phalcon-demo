<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

namespace Admin\Controllers;

use Auth\Models\EmailUser;

class AdminController extends \BaseController
{
    public function indexAction()
    {
        $users = EmailUser::getAll();
        $this->view->setVar('users', $users);
        $this->view->pick($this->_localizePath('content/admin'));
    }
}
