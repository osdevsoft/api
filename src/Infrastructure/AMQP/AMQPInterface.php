<?php

namespace Osds\Api\Infrastructure\AMQP;

interface AMQPInterface {

    public function connect();

    public function publish($queue, $message);

}