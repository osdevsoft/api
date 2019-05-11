<?php

namespace Osds\Api\Application;

class BaseConsumer
{

    public function log($message)
    {
        $caller = $this->getCallerClass($this);
        echo $caller . ' :: ' . $message . PHP_EOL . PHP_EOL;
    }

    protected function getCallerClass($class)
    {
        return preg_replace('/.*\\\/', '', get_class($class));
    }

}
