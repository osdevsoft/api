<?php

namespace Osds\Api\Application;

class BaseConsumer
{

    public function log($message)
    {
        $caller = $this->getCallerClass();
        echo $caller . ' :: ' . $message;
    }

    private function getCallerClass()
    {
        return preg_replace('/.*\\\/', '', get_class($this));
    }

}
