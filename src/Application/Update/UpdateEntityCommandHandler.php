<?php

namespace Osds\Api\Application\Update;

use Osds\Api\Domain\Bus\Command\CommandHandler;

final class UpdateEntityCommandHandler implements CommandHandler
{
    private $use_case;

    public function __construct(UpdateEntityUseCase $use_case)
    {
        $this->use_case = $use_case;
    }

    public function handle(UpdateEntityCommand $command)
    {
        return $this->use_case->execute(
            $command->entity(),
            $command->id(),
            $command->data()
            );
    }
}
