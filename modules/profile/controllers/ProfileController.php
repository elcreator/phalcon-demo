<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

namespace Profile\Controllers;

use Auth\Models\EmailUser;
use Phalcon\Filter\Validation\Validator;
use Phalcon\Filter\Validation;

class ProfileController extends \BaseController
{
    const URL_PROFILE = '/profile';
    protected $_stylesheets = ['/css/profile/profile.css'];

    public function indexAction()
    {
        $user = $this->_getUser();
        $this->view->pick($this->_localizePath('registered/profile'));
        $this->view->setVar('user', $user);
    }

    public function meAction()
    {
        $user = $this->_getUser();
        $result = new \stdClass();
        $result->token = $user->token;
        $result->fullname = $user->fullname;

        $this->_outputJson($result);
    }

    public function searchAction()
    {
        $result = [];
        $input = $this->_inputJson();
        if (empty($input->query) || strlen($input->query) < 3) {
            $this->_outputJson($result);
            return;
        };
        $users = EmailUser::find([
            'conditions' => 'fullname LIKE :value:',
            'bind' => ['value' => '%' . $input->query . '%'],
            'limit' => 10
        ]);
        foreach ($users as $user) {
            $item = new \stdClass();
            $item->id = $user->id;
            $item->name = $user->fullname;
            array_push($result, $item);
        }
        $this->_outputJson($result);
    }

    public function emailAction()
    {
        $email = $this->request->getPost('new-email', 'email', null, true);
        $emailRepeat = $this->request->getPost('new-email-repeat', 'email', null, true);
        if (empty($email) || empty($emailRepeat))
        {
            $this->_errors[] = __('E-mail and confirm e-mail fields should be valid.');
        }
        if ($email !== $emailRepeat)
        {
            $this->_errors[] = __('E-mail and confirm e-mail fields does not match.');
        }

        $userId = $this->session->get(EmailUser::SESSION_ID);
        /** @var EmailUser $user */
        $user = EmailUser::findFirst(array("id = ?0", "bind" => array($userId)));

        $oldEmail = $user->email;
        if ($email === $oldEmail)
        {
            $this->_errors[] = __('E-mail is the same as previous one.');
        }
        if (count($this->_errors))
        {
            $this->_processErrors(self::URL_PROFILE);
            return;
        }

        $user->email = $email;
        $user->save();

        $this->flashSession->success(__('E-mail was successfully changed.'));
        $this->mailer->send(
            __('E-mail change notification.'),
            __("Your e-mail %s was successfully changed to %s.", $oldEmail, $email),
            $email,
            $oldEmail
        );
        $this->_redirect(self::URL_PROFILE);
    }

    public function passwordAction()
    {
        $password = $this->request->getPost('new-password', 'string', null, true);
        $passwordRepeat = $this->request->getPost('new-password-repeat', 'string', null, true);
        if ($password !== $passwordRepeat)
        {
            $this->_errors[] = __('Password and password confirmation does not match.');
        }
        if (mb_strlen($password) < 6)
        {
            $this->_errors[] = sprintf(__('Password containing %s symbols is easy to hack. Should be at least 6.'), mb_strlen($password));
        }
        if (count($this->_errors))
        {
            $this->_processErrors(self::URL_PROFILE);
            return;
        }

        $userId = $this->session->get(EmailUser::SESSION_ID);
        /** @var EmailUser $user */
        $user = EmailUser::findFirst(["id = ?0", "bind" => [$userId]]);
        //TODO: move to auth
        $user->password = $this->security->hash($password);
        $user->save();
        $this->flashSession->success(__('Password changed successfully.'));
        $this->mailer->send(
            __('Password change notification'),
            __('Your password was changed.'),
            $user->email
        );
        $this->_redirect(self::URL_PROFILE);
    }

    public function fullnameAction()
    {
        $validator = new Validation();
        $fullName = $this->request->getPost('username', 'string');
        $validator->rules('username', [
            new Validator\PresenceOf(['message' => __('Input your full name.')]),
            new Validator\StringLength([
                'min' => 4,  'messageMinimum' => __('Input your full name 4 letters min'),
                'max' => 50, 'messageMaximum' => __('Input your full name 50 letters max')
            ]),
        ]);
        $validator->validate($this->request->getPost());
        foreach($validator->getMessages() as $message)
        {
            $this->_errors[] = $message;
        }
        if (count($this->_errors))
        {
            $this->_processErrors(self::URL_PROFILE);
            return;
        }

        $user = $this->_getUser();
        $user->fullname = $fullName;
        $user->save();
        $this->_redirect(self::URL_PROFILE);
    }

    /**
     * @return EmailUser
     */
    private function _getUser()
    {
        $userId = $this->_myId();
        $user = EmailUser::findFirst([
            'id = ?0',
            'bind' => [$userId]
        ]);
        return $user;
    }
}
