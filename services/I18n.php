<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

class I18n
{
    public $languageCodes = [];
    public $defaultLanguageCode;
    private $_languageCode;

    public function detectLanguageCode()
    {
        $locale = isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])
            ? locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE'])
            : null;
        return locale_lookup($this->languageCodes, empty($locale) ? $this->defaultLanguageCode : $locale, false,
            $this->defaultLanguageCode);
    }

    public function setLanguageCode($languageCode)
    {
        if (!in_array($languageCode, $this->languageCodes)) {
            throw new \InvalidArgumentException('Incorrect language code ' . $languageCode);
        }
        $this->_languageCode = $languageCode;
    }

    public function getLanguageCode()
    {
        return isset($this->_languageCode) ? $this->_languageCode : $this->defaultLanguageCode;
    }

    public function getLanguageCodes()
    {
        return $this->languageCodes;
    }
}