<?php

declare(strict_types=1);

namespace Ddrv\Env\Variable;

use BackedEnum;
use Ddrv\Env\Exception\CastTypeError;
use Stringable;
use TypeError;
use UnitEnum;
use ValueError;

final class Variable implements Stringable
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function bool(): bool
    {
        $prepared = trim(strtolower($this->value));
        if (in_array($prepared, ['yes', 'true', '1', 'on'], true)) {
            return true;
        }
        if (in_array($prepared, ['no', 'false', '0', 'off', ''], true)) {
            return false;
        }

        throw new CastTypeError(sprintf('Cannot cast "%s" to bool', $this->value));
    }

    public function int(): int
    {
        if (!is_numeric($this->value) || str_contains($this->value, '.')) {
            throw new CastTypeError(sprintf('Cannot cast "%s" to int', $this->value));
        }

        return (int) $this->value;
    }

    public function float(): float
    {
        if (!is_numeric($this->value)) {
            throw new CastTypeError(sprintf('Cannot cast "%s" to float', $this->value));
        }

        return (float) $this->value;
    }

    public function string(): string
    {
        return $this->value;
    }

    /**
     * @template T of BackedEnum
     * @param class-string<T> $enumClass
     * @return T
     */
    public function enum(string $enumClass): BackedEnum
    {
        if (!is_a($enumClass, BackedEnum::class, true)) {
            $type = class_exists($enumClass) ? sprintf('class-string<%s>', $enumClass) : 'string';
            throw new TypeError(sprintf(
                '%s(): Argument #1 ($enumClass) must be of type class-string<%s>, %s given',
                __METHOD__,
                UnitEnum::class,
                $type,
            ));
        }

        try {
            $environment = is_string($enumClass::cases()[0]->value) ? $this->value : $this->int();
            return $enumClass::from($environment);
        } catch (TypeError | ValueError $exception) {
            throw new CastTypeError(sprintf('Cannot cast "%s" to %s', $this->value, $enumClass), 0, $exception);
        }
    }

    /**
     * @template T of UnitEnum
     * @param class-string<T> $enumClass
     * @return T
     */
    public function enumByName(string $enumClass): UnitEnum
    {
        if (!is_a($enumClass, UnitEnum::class, true)) {
            $type = class_exists($enumClass) ? sprintf('class-string<%s>', $enumClass) : 'string';
            throw new TypeError(sprintf(
                '%s(): Argument #1 ($enumClass) must be of type class-string<%s>, %s given',
                __METHOD__,
                UnitEnum::class,
                $type,
            ));
        }

        foreach ($enumClass::cases() as $enum) {
            if ($enum->name === $this->value) {
                return $enum;
            }
        }

        throw new CastTypeError(sprintf('Cannot cast "%s" to %s', $this->value, $enumClass));
    }

    public function __toString(): string
    {
        return $this->string();
    }
}
