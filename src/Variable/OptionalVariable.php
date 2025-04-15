<?php

declare(strict_types=1);

namespace Ddrv\Env\Variable;

use BackedEnum;
use TypeError;
use UnitEnum;

final class OptionalVariable
{
    private ?RequiredVariable $variable;

    public function __construct(?string $value)
    {
        $this->variable = is_null($value) ? null : new RequiredVariable($value);
    }

    public function bool(bool $default): bool
    {
        if (is_null($this->variable)) {
            return $default;
        }

        return $this->variable->bool();
    }

    public function int(int $default): int
    {
        if (is_null($this->variable)) {
            return $default;
        }

        return $this->variable->int();
    }

    public function float(float $default): float
    {
        if (is_null($this->variable)) {
            return $default;
        }

        return $this->variable->float();
    }

    public function string(string $default): string
    {
        if (is_null($this->variable)) {
            return $default;
        }

        return $this->variable->string();
    }

    /**
     * @template T of BackedEnum
     * @param class-string<T> $enumClass
     * @param T $default
     * @return T
     */
    public function enum(string $enumClass, BackedEnum $default): BackedEnum
    {
        if (!is_a($default, $enumClass)) {
            throw new TypeError(sprintf(
                '%s(): Argument #2 ($default) must be of type %s, %s given',
                __METHOD__,
                $enumClass,
                get_class($default)
            ));
        }

        if (is_null($this->variable)) {
            return $default;
        }

        return $this->variable->enum($enumClass);
    }

    /**
     * @template T of UnitEnum
     * @param class-string<T> $enumClass
     * @param T $default
     * @return T
     */
    public function unitEnum(string $enumClass, UnitEnum $default): UnitEnum
    {
        if (!is_a($default, $enumClass)) {
            throw new TypeError(sprintf(
                '%s(): Argument #2 ($default) must be of type %s, %s given',
                __METHOD__,
                $enumClass,
                get_class($default)
            ));
        }

        if (is_null($this->variable)) {
            return $default;
        }

        return $this->variable->unitEnum($enumClass);
    }
}
