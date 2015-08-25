<?php

namespace Alchemy\PhraseanetBundle\Debug;

use Guzzle\Common\Event;
use Guzzle\Http\Message\Request;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class TimelinePlugin implements EventSubscriberInterface
{

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            'request.before_send' => array('startRequest'),
            'request.complete' => array('stopRequest')
        );
    }

    /**
     * @var Stopwatch
     */
    private $stopwatch;

    public function __construct(Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
    }

    public function startRequest(Event $event)
    {
        $this->stopwatch->start(
            $this->getRequestName($event['request']),
            'phraseanet'
        );
    }

    public function stopRequest(Event $event)
    {
        $this->stopwatch->stop($this->getRequestName($event['request']));
    }

    private function getRequestName(Request $request)
    {
        return (string) $request->getPath();
    }
}
