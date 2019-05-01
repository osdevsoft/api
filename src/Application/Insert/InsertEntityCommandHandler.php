<?php

namespace Osds\Api\Application\Insert;

use Osds\Api\Domain\Bus\Command\CommandHandler;
use Osds\Api\Infrastructure\AMQP\AMQPInterface;

//use Osds\Api\Infrastructure\AMQP\RabbitMQ;

final class InsertEntityCommandHandler implements CommandHandler
{
    private $useCase;
    private $amqp;

    public function __construct(
        InsertEntityUseCase $useCase
        , AMQPInterface $amqp

    )
    {
        $this->useCase = $useCase;
        $this->amqp = $amqp;
    }

    public function handle(InsertEntityCommand $command)
    {
        if(1) {

            try {
             $this->amqp->publish('my_cue', $command->getPayload());
             return $command->uuid();
            } catch(\Exception $e) {
                dd($e->getMessage());
            }

        } else {

            return $this->useCase->execute(
                $command->entity(),
                $command->uuid(),
                $command->data()
            );
        }
    }
}
