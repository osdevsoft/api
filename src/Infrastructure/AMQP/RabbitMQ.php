<?php

namespace Osds\Api\Infrastructure\AMQP;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ implements AMQPInterface {

    private $connection;
    private $channel;


    public function __construct() {

        $this->connect();

    }


    public function connect() {

        $this->connection = new AMQPStreamConnection('rabbitmq', 5672, 'rabbitmq', 'rabbitmq');
        $this->channel = $this->connection->channel();

    }

    public function publish($queue, $message) {

        $this->channel->queue_declare($queue, false, false, false, false);

        $msg = new AMQPMessage($message);
        $this->channel->basic_publish($msg, '', $queue);

        $this->channel->close();
        $this->connection->close();
    }

}