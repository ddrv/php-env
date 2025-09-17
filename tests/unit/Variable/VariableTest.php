<?php

declare(strict_types=1);

namespace Tests\Unit\Ddrv\Env\Variable;

use BackedEnum;
use Ddrv\Env\Exception\CastTypeError;
use Ddrv\Env\Variable\Variable;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tests\Code\Ddrv\Env\IntEnum;
use Tests\Code\Ddrv\Env\StringEnum;
use Tests\Code\Ddrv\Env\UntypedEnum;
use UnitEnum;

final class VariableTest extends TestCase
{
    #[DataProvider('provideBool')]
    public function testBool(string $raw, bool $expected): void
    {
        $variable = new Variable($raw);
        Assert::assertSame($expected, $variable->bool());
    }

    #[DataProvider('provideBoolError')]
    public function testBoolError(string $raw): void
    {
        $variable = new Variable($raw);

        $this->expectException(CastTypeError::class);
        $variable->bool();
    }

    #[DataProvider('provideInt')]
    public function testInt(string $raw, int $expected): void
    {
        $variable = new Variable($raw);
        Assert::assertSame($expected, $variable->int());
    }

    #[DataProvider('provideIntError')]
    public function testIntError(string $raw): void
    {
        $variable = new Variable($raw);

        $this->expectException(CastTypeError::class);
        $variable->int();
    }

    #[DataProvider('provideFloat')]
    public function testFloat(string $raw, float $expected): void
    {
        $variable = new Variable($raw);
        Assert::assertSame($expected, $variable->float());
    }

    #[DataProvider('provideFloatError')]
    public function testFloatError(string $raw): void
    {
        $variable = new Variable($raw);

        $this->expectException(CastTypeError::class);
        $variable->float();
    }

    /**
     * @template T of BackedEnum
     * @param class-string<T> $enumClassName
     * @param T $expected
     */
    #[DataProvider('provideEnum')]
    public function testEnum(string $raw, string $enumClassName, BackedEnum $expected): void
    {
        $variable = new Variable($raw);
        Assert::assertSame($expected, $variable->enum($enumClassName));
    }

    /**
     * @param class-string<BackedEnum> $enumClassName
     */
    #[DataProvider('provideEnumError')]
    public function testEnumError(string $raw, string $enumClassName): void
    {
        $variable = new Variable($raw);

        $this->expectException(CastTypeError::class);
        $variable->enum($enumClassName);
    }

    /**
     * @template T of UnitEnum
     * @param class-string<T> $enumClassName
     * @param T $expected
     */
    #[DataProvider('provideEnumByName')]
    public function testEnumByName(string $raw, string $enumClassName, UnitEnum $expected): void
    {
        $variable = new Variable($raw);
        Assert::assertSame($expected, $variable->enumByName($enumClassName));
    }

    /**
     * @param class-string<UnitEnum> $enumClassName
     */
    #[DataProvider('provideEnumByNameError')]
    public function testEnumByNameError(string $raw, string $enumClassName): void
    {
        $variable = new Variable($raw);

        $this->expectException(CastTypeError::class);
        $variable->enumByName($enumClassName);
    }

    /**
     * @return array{0:string, 1: bool}[]
     */
    public static function provideBool(): iterable
    {
        return [
            ['YES', true],
            ['trUe', true],
            ['1', true],
            ['on', true],
            [' yes ', true],
            ['', false],
            [' ', false],
            ['NO', false],
            ['FALse', false],
            ['0', false],
            ['off', false],
            [' no ', false],
        ];
    }

    /**
     * @return array{0:string}[]
     */
    public static function provideBoolError(): iterable
    {
        return [
            ['_YES'],
            ['random value'],
            ['10'],
        ];
    }

    /**
     * @return array{0:string, 1: int}[]
     */
    public static function provideInt(): iterable
    {
        $rand = rand(PHP_INT_MIN, PHP_INT_MAX);
        return [
            ['1', 1],
            ['42', 42],
            ['-1', -1],
            [(string)$rand, $rand],
        ];
    }

    /**
     * @return array{0:string}[]
     */
    public static function provideIntError(): iterable
    {
        return [
            ['_YES'],
            ['random value'],
            ['10.0'],
            ['-.1'],
        ];
    }

    /**
     * @return array{0:string, 1: float}[]
     */
    public static function provideFloat(): iterable
    {
        return [
            ['0', 0.0],
            ['4.2', 4.2],
            ['-2364723.234324', -2364723.234324],
        ];
    }

    /**
     * @return array{0:string}[]
     */
    public static function provideFloatError(): iterable
    {
        return [
            ['_YES'],
            ['random value'],
            ['0.10.0'],
            ['-.1e'],
        ];
    }

    /**
     * @template T of BackedEnum
     * @return array{0:string, 1: class-string<T>, 2: T}[]
     */
    public static function provideEnum(): iterable
    {
        foreach (IntEnum::cases() as $value) {
            yield [(string)$value->value, IntEnum::class, $value];
        }
        foreach (StringEnum::cases() as $value) {
            yield [$value->value, StringEnum::class, $value];
        }
    }

    /**
     * @template T of BackedEnum
     * @return array{0:string, 1: class-string<T>}[]
     */
    public static function provideEnumError(): iterable
    {
        return [
            ['0', IntEnum::class],
            ['a', IntEnum::class],
            ['d', StringEnum::class],
            ['1', StringEnum::class],
        ];
    }

    /**
     * @template T of UnitEnum
     * @return array{0:string, 1: class-string<T>, 2: T}[]
     */
    public static function provideEnumByName(): iterable
    {
        foreach (IntEnum::cases() as $value) {
            yield [$value->name, IntEnum::class, $value];
        }
        foreach (StringEnum::cases() as $value) {
            yield [$value->name, StringEnum::class, $value];
        }
        foreach (UntypedEnum::cases() as $value) {
            yield [$value->name, UntypedEnum::class, $value];
        }
    }

    /**
     * @template T of UnitEnum
     * @return array{0:string, 1: class-string<T>}[]
     */
    public static function provideEnumByNameError(): iterable
    {
        return [
            ['0', IntEnum::class],
            ['a', IntEnum::class],
            ['d', StringEnum::class],
            ['1', StringEnum::class],
            ['one', UntypedEnum::class],
        ];
    }
}
