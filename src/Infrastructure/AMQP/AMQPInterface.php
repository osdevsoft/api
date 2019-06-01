<?php

namespace Osds\Api\Infrastructure\AMQP;

interface AMQPInterface
{
    public function connect($server, $port, $user, $password);

    public function publish($queue, $message);
}
