<?php
/**
 * @file    Translations.php
 * @brief
 * @author  Artur Kirilyuk (artur.kirilyuk@gmail.com)
 * @package Admin\models
 */

class Translations extends \BaseModel
{
    public function initialize()
    {
        $translations = new \Gettext\Translations();
        $this->_parseTranslations(MODULES_DIR, 'php', new \Gettext\Extractors\PhpCode(), $translations);
        $this->_parseTranslations(VIEWS_DIR, 'twig', new \Gettext\Extractors\JsCode(), $translations);
        $translations->toPoFile(I18N_POT);
        $this->_parsePoFiles(I18N_DIR);
    }

    /**
     * @param string $path
     * @param string $extension
     * @param \Gettext\Extractors\ExtractorInterface $extractor
     * @param \Gettext\Translations $translations
     */
    private function _parseTranslations($path, $extension, \Gettext\Extractors\ExtractorInterface $extractor,
        \Gettext\Translations $translations)
    {
        $templates = $this->_getIterator($path, $extension);
        foreach ($templates as $template => $value)
        {
            $extractor->fromFile($template, $translations);
        }
    }

    /**
     * @param string $path
     */
    private function _parsePoFiles($path)
    {
        $poFiles = $this->_getIterator($path, 'po');
        foreach ($poFiles as $poFile => $value)
        {
            $translations = new \Gettext\Translations();
            $extractor = new \Gettext\Extractors\Po();
            $extractor->fromFile($poFile, $translations);
            $fileInfo = pathinfo($poFile);
            $language = $fileInfo['filename'];
            $outputFile = I18N_CACHE_DIR . $language . '.txt';
            $translations->toPhpArrayFile($outputFile);
        }
    }

    /**
     * @param string $path
     * @param string $extension
     * @return \RegexIterator
     */
    private function _getIterator($path, $extension)
    {
        return new \RegexIterator(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)),
            '~^.+\.' . $extension . '$~');
    }
}
