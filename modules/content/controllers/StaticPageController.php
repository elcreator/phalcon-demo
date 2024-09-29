<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

namespace Content\Controllers;

class StaticPageController extends \BaseController
{
    public function indexAction($page = NULL)
    {
        $this->_processIndexPage($page);
    }

    public function notFoundAction()
    {
        $lang = $this->i18n->getLanguageCode();
        $this->_registerTranslator($lang);
        $this->view->setVar('lang', $lang);
        if (!$this->view->getVar('alias'))
        {
            $this->view->setVar('alias', '404');
        }
        $this->view->pick('404');
        $this->response->setStatusCode(404);
    }
}
