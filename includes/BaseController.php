<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

/** @property \Phalcon\Config $config */
/** @property \Mailer $mailer */
/** @property \I18n $i18n */
class BaseController extends \Phalcon\Mvc\Controller implements \Phalcon\Di\InjectionAwareInterface
{
    const CONTENT_DIR = 'content/';
    protected $_errors = [];
    protected $_stylesheets = [];

    public function initialize()
    {
        $lang = $this->i18n->getLanguageCode();
        $this->view->setVars([
            'lang' => $lang,
            'languages' => $this->i18n->getLanguageCodes(),
            'stylesheets' => $this->_stylesheets,
        ]);
        $this->_registerTranslator($lang);
    }

    /**
     * @return int
     * @throws AuthException
     */
    protected function _myId()
    {
        $userId = $this->session->get('id');
        if (!$userId)
        {
            throw new \AuthException('Session expired');
        }
        return (int) $userId;
    }

    /**
     * @return int
     */
    protected function _adminId() {
        return 1;
    }

    /**
     * @param string $lang
     */
    protected function _registerTranslator($lang)
    {
        $cacheFileName = I18N_CACHE_DIR . $lang . '.txt';
        if (!file_exists($cacheFileName))
        {
            new \Translations();
        }
        $translations = \Gettext\Translations::fromJsonDictionaryFile(I18N_DIR . $lang . '.json');
        $translator = new \Gettext\Translator();
        $translator->loadTranslations($translations);
        $translator->register();
    }

    protected function _processIndexPage($page)
    {
        if (empty($page))
        {
            $page = 'index~' . $this->i18n->getLanguageCode();
        }
        $staticPageFile = self::CONTENT_DIR . $page;
        $chunks = explode('~', $page);
        if (count($chunks) === 1) {
            $this->_redirect($this->_localizePath($page));
            return;
        }
        if (count($chunks) !== 2 || !in_array($chunks[1], $this->i18n->getLanguageCodes()))
        {
            $lang = $this->i18n->getLanguageCode();
            $this->_registerTranslator($lang);
            $this->view->setVar('lang', $lang);
            $this->view->setVar('alias', '404');
            $this->notFoundAction();
            return;
        }
        list($pageName, $lang) = $chunks;
        $this->_registerTranslator($lang);
        $this->view->setVar('lang', $lang);
        $this->view->setVar('alias', $pageName);

        if (!$this->view->exists($staticPageFile))
        {
            $lang = $this->i18n->getLanguageCode();
            $this->_registerTranslator($lang);
            $this->notFoundAction();
            return;
        }
        $this->i18n->setLanguageCode($lang);
        $this->view->pick($staticPageFile);
    }

    /**
     * @param string $path
     */
    protected function _localizePath($path)
    {
        return $path . '~' . $this->i18n->getLanguageCode();
    }

    /**
     * @param string $redirectUrl
     */
    protected function _processErrors($redirectUrl)
    {
        array_walk($this->_errors, function ($error)
        {
            $this->flashSession->error($error);
        });
        $this->_redirect($redirectUrl);
    }

    /**
     * @param string|null $schemaPath
     * @return stdClass
     * @throws Exception
     */
    protected function _inputJson($schemaPath = null)
    {
        $data = $this->request->getJsonRawBody();
        if (is_null($schemaPath)) {
            return $data;
        }
        $schema = json_decode(trim(file_get_contents($schemaPath)));

        $validator = new JsonSchema\Validator();
        $validator->validate($data, $schema);

        if ($validator->isValid()) {
            return $data;
        } else {
            $errors = "JSON validation failed:\n";
            foreach ($validator->getErrors() as $error) {
                $errors .= sprintf("[%s] %s\n", $error['property'], $error['message']);
            }
            throw new \Exception($errors);
        }
    }

    /**
     * @param mixed $result
     */
    protected function _outputJson($result, $code = 200)
    {
        $this->view->disable();
        $this->response->setContentType('application/json');
        $this->response->setJsonContent($result, JSON_UNESCAPED_UNICODE);
        $this->response->setStatusCode($code);
        $this->response->send();
    }

    protected function _outputNoContent()
    {
        $this->view->disable();
        $this->response->setContentType('application/json');
        $this->response->setStatusCode(204);
        $this->response->send();
    }

    /**
     * @param string $redirectUrl
     */
    protected function _redirect($redirectUrl)
    {
        $this->response->redirect($redirectUrl);
        $this->view->disable();
    }
}
