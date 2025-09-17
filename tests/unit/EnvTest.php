<?php

declare(strict_types=1);

namespace Tests\Unit\Ddrv\Env;

use Ddrv\Env\Env;
use Ddrv\Env\Exception\VariableUndefined;
use Ddrv\Env\VariableProvider\MemoryVariableProvider;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class EnvTest extends TestCase
{
    /** @var array<string, string|null> */
    private const ENV = [
        'TEST_VAR_1' => 'one',
        'TEST_VAR_2' => 'two',
        'TEST_VAR_3' => 'three',
        'TEST_VAR_4' => 'four',
    ];

    #[DataProvider('provideVariables')]
    public function testOptional(string $variable, string $value): void
    {
        $env = $this->getEnv();

        $actual = $env->optional($variable);
        Assert::assertNotNull($actual);
        Assert::assertSame($value, $actual->string());
    }

    #[DataProvider('provideUndefinedVariables')]
    public function testOptionalWithUndefinedVariable(string $variable): void
    {
        $env = $this->getEnv();
        Assert::assertNull($env->optional($variable));
    }

    /**
     * @throws VariableUndefined
     */
    #[DataProvider('provideVariables')]
    public function testRequired(string $variable, string $value): void
    {
        $env = $this->getEnv();
        Assert::assertSame($value, $env->required($variable)->string());
    }

    #[DataProvider('provideUndefinedVariables')]
    public function testRequiredWithUndefinedVariable(string $variable): void
    {
        $env = $this->getEnv();

        $this->expectException(VariableUndefined::class);
        $env->required($variable);
    }

    #[DataProvider('provideVariableNames')]
    public function testHas(string $variable): void
    {
        $env = $this->getEnv();
        Assert::assertTrue($env->has($variable));
    }

    #[DataProvider('provideUndefinedVariables')]
    public function testHasWithUndefinedVariable(string $variable): void
    {
        $env = $this->getEnv();
        Assert::assertFalse($env->has($variable));
    }

    /**
     * @return array{0: string, 1: string}[]
     */
    public static function provideVariables(): iterable
    {
        foreach (self::ENV as $variable => $value) {
            yield [$variable, $value];
        }
    }

    /**
     * @return array{0: string}[]
     */
    public static function provideVariableNames(): iterable
    {
        foreach (array_keys(self::ENV) as $variable) {
            yield [$variable];
        }
    }

    /**
     * @return array{0: string}[]
     */
    public static function provideUndefinedVariables(): iterable
    {
        foreach (array_keys(self::ENV) as $variable) {
            yield [sprintf('UNDEFINED_%s', $variable)];
        }
    }

    private function getEnv(): Env
    {
        return new Env(new MemoryVariableProvider(self::ENV));
    }
}
