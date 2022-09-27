<?php

namespace App\Enums;

use InvalidArgumentException;

/**
 * Trait EnumTrait
 * @package App\Enums
 */
trait EnumTrait
{
    /**
     * @var int|string
     */
    private $value;

    public function __construct($value)
    {
        if (false === \in_array($value, static::variants(), true)) {
            throw new InvalidArgumentException(
                \sprintf('Invalid enumeration argument "%s". Allowed arguments are "%s"', $value, \implode(',', static::variants()))
            );
        }

        $this->value = $value;
    }

    public function is($value): bool
    {
        return $value === $this->value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getAsText(): string
    {
        return static::list()[$this->value];
    }

    final public function __toString(): string
    {
        return (string)$this->value;
    }

    public static function create($value): self
    {
        return new static($value);
    }

    public static function list(): array
    {
        // generate variants list with empty names
        return \array_combine(
            static::variants(), \array_map(static fn() => '', static::variants())
        );
    }

    abstract public static function variants(): array;
}
