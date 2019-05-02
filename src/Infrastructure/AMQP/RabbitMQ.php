<?php

namespace Osds\Api\Infrastructure\AMQP;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ implements AMQPInterface {

    private $connection;
    private $channel;


    public function __construct(
        $server,
        $port,
        $user,
        $password
    )
    {
        $this->connect($server, $port, $user, $password);
    }


    public function connect($server, $port, $user, $password)
    {
        $this->connection = new AMQPStreamConnection($server, $port, $user, $password);
        $this->channel = $this->connection->channel();

    }

    public function publish($queue, $message)
    {

        $this->channel->queue_declare($queue, false, true, false, false);

        $msg = new AMQPMessage($message);
        $this->channel->basic_publish($msg, '', $queue);

        $this->channel->close();
        $this->connection->close();
    }

}