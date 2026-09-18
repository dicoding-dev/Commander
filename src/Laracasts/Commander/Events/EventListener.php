<?php namespace Laracasts\Commander\Events;

use ReflectionClass;

abstract class EventListener {

    /**
     * Handle the event
     *
     * @param $event
     * @return mixed
     */
    public function handle($event)
    {
        $event = static::unwrapEvent(...func_get_args());

        $eventName = $this->getEventName($event);

        if ($this->listenerIsRegistered($eventName))
        {
            return call_user_func([$this, 'when'.$eventName], $event);
        }
    }

    /**
     * L13's event dispatcher invokes wildcard listeners as ($eventName, $payload); the
     * pre-L5 dispatcher passed the event object directly. Kept as a one-param signature so
     * subclasses that override handle($event) stay compatible; use func_get_args() to reach
     * the payload. Returns the domain event object.
     *
     * @param  mixed  $event
     * @param  array  $payload
     * @return mixed
     */
    protected static function unwrapEvent($event, $payload = [])
    {
        if (is_string($event) && isset($payload[0]) && is_object($payload[0]))
        {
            return $payload[0];
        }

        return $event;
    }

    /**
     * Figure out what the name of the class is.
     *
     * @param $event
     * @return string
     */
    protected function getEventName($event)
    {
        return (new ReflectionClass($event))->getShortName();
    }

    /**
     * Determine if a method in the subclass is registered
     * for this particular event.
     *
     * @param $eventName
     * @return bool
     */
    protected function listenerIsRegistered($eventName)
    {
        $method = "when{$eventName}";

        return method_exists($this, $method);
    }

} 