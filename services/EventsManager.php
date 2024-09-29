<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

class EventsManager extends \Phalcon\Events\Manager
{
    /**
     * @param \Phalcon\Di\Di $di
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
