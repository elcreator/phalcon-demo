<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

require '../includes/Constants.php';

$startTime = microtime(true);
require_once COMPOSER_PATH;

try
{
    $loader = new \Phalcon\Autoload\Loader();
    $loader->setFiles(['../vendor/phalcon.eager-loading/src/EagerLoadingTrait.php'], true);
    $loader->setNamespaces(['Phalcon\\Mvc\\Model\\EagerLoading' => '../vendor/phalcon.eager-loading/src/EagerLoading'], true);
    $loader->setDirectories([INCLUDES_DIR, SERVICES_DIR]);
    $loader->register();

    $di = new \DiFactory;
    $app = new \App($di);

    $appHandler = $app->handle($_SERVER['REQUEST_URI']);
    $app->log->info(number_format(microtime(true) - $startTime, 3) . ' '
        . $app->request->getMethod() . ' ' . $app->request->getURI());
    echo $appHandler->getContent();
}
catch (\NotFoundException $e)
{
    $app->response->setStatusCode(404);
    $app->response->setContent($e->getMessage());
    $app->response->send();
}
catch (\AuthException $e)
{
    $app->log->info($e->getMessage());
    if ($app->session->exists()) {
        $app->session->destroy();
    }
    $app->response->redirect('/auth~' . $app->i18n->getLanguageCode());
    $app->response->sendHeaders();
}
catch (\Exception $e)
{
    $app->log->critical("Caught Exception: " . $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL);
    $app->response->setStatusCode(500);
    $app->response->sendHeaders();
}
