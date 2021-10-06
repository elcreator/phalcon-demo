<?php
/**
 * Created by PhpStorm.
 * User: Artur.Kyryliuk
 * Date: 5/16/16
 * Time: 3:09 PM
 */

class EventsManager extends \Phalcon\Events\Manager
{
    /**
     * @param \Phalcon\Di $di
     */
    public function __construct($di)
    {
        $this->attach('dispatch:beforeExecuteRoute',
            function (\Phalcon\Events\Event $event, \Phalcon\Mvc\Dispatcher $dispatcher) use ($di)
            {
                return new \BeforeExecuteRouteEvent($di, $dispatcher);
            });
    }
}
