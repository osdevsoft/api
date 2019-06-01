<?php

namespace Osds\Api\Application;

class BaseConsumer
{

    public function log($message)
    {
        $caller = $this->getCallerClass($this);
        echo PHP_EOL . PHP_EOL . date('[Y-m-d H:i:s] ') . $caller . ' :: ' . $message . PHP_EOL;
    }

    protected function getCallerClass($class)
    {
        return preg_replace('/.*\\\/', '', get_class($class));
    }
}
