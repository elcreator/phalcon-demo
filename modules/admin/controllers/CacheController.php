<?php
/**
 * Created by PhpStorm.
 * User: Artur
 * Date: 11.05.2017
 * Time: 00:04
 */

namespace Admin\Controllers;


class CacheController extends \BaseController
{
    public function clearViewAction()
    {
        $this->_recursiveClear(VIEWS_CACHE_DIR);
        $this->flashSession->success(__('View cache cleared.'));
        $this->_redirect('/admin');
    }

    public function clearConfigAction()
    {
        unlink(CONFIG_CACHE_PATH);
        $this->flashSession->success(__('Config cache cleared.'));
        $this->_redirect('/admin');
    }

    public function clearTranslationAction()
    {
        $this->_recursiveClear(I18N_CACHE_DIR);
        new \Translations();
        $this->flashSession->success(__('Translation cache cleared.'));
        $this->_redirect('/admin');
    }

    /**
     * @param string $path
     */
    private function _recursiveClear($path)
    {
        $directoryIterator = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
        $iteratorIterator = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($iteratorIterator as $item) {
            $item->isDir() ? rmdir($item) : unlink($item);
        }
    }
}
