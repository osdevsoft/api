<?php

declare(strict_types = 1);

namespace Osds\Api\Domain\ValueObject;

use InvalidArgumentException;
use Ramsey\Uuid\Codec\TimestampFirstCombCodec;
use Ramsey\Uuid\Generator\CombGenerator;
use Ramsey\Uuid\UuidFactory;


class Uuid
{
    private $value;

    public function __construct(string $value)
    {
//        $this->guard($value);

        $this->value = $value;
    }

    public static function random(): self
    {
        $factory = new UuidFactory;

        $factory->setRandomGenerator(new CombGenerator(
            $factory->getRandomGenerator(),
            $factory->getNumberConverter()
        ));

        $factory->setCodec(new TimestampFirstCombCodec(
            $factory->getUuidBuilder()
        ));

        return new self($factory->uuid4()->toString());
    }

    public function value(): string
    {
        return $this->value;
    }

    private function guard($id): void
    {
        if (!RamseyUuid::isValid($id)) {
            throw new InvalidArgumentException(
                sprintf('<%s> does not allow the value <%s>.', static::class, is_scalar($id) ? $id : gettype($id))
            );
        }
    }

    public function __toString()
    {
        return $this->value();
    }
}
