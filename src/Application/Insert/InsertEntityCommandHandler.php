<?php

namespace Osds\Api\Application\Delete;

use Osds\Api\Domain\Bus\Command\CommandHandler;

final class DeleteEntityCommandHandler implements CommandHandler
{
    private $use_case;

    public function __construct(DeleteEntityUseCase $use_case)
    {
        $this->use_case = $use_case;
    }

    public function handle(DeleteEntityCommand $command)
    {
        return $this->use_case->execute(
            $command->entity(),
            $command->id()
            );
    }
}
