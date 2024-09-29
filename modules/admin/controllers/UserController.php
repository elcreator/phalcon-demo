<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

namespace Admin\Controllers;

use Auth\Models\EmailUser;

class UserController extends \BaseController
{
    public function getAction($id)
    {
        $user = EmailUser::getById($id);
        $this->view->setVar('user', $user);
        $this->view->pick('admin/user');
    }

    public function deleteAction($id)
    {
        $user = EmailUser::getById($id);
        $user->delete();
        $this->_outputNoContent();
    }

    public function updateAction($id)
    {
        $input = $this->request->getJsonRawBody();
        /** @var EmailUser $user */
        $user = EmailUser::getById($id);

        $user->isBanned = $input->isBanned;

        if (!$user->update()) {
            $this->_outputJson($user->getMessages());
            return;
        }
        $this->_outputJson($user);
    }
}
