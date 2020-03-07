<?php

namespace Osds\Api\Application;

class BaseConsumer
{

    public function log($message, $severity = 'info')
    {
        $caller = $this->getCallerClass($this);
        echo PHP_EOL . date('[Y-m-d H:i:s]') . ' [severity:'.$severity.'] ' . $caller . ' :: ' . $message;
    }

    protected function getCallerClass($class)
    {
        return preg_replace('/.*\\\/', '', get_class($class));
    }
}
