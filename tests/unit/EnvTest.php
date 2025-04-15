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
        'TEST_VAR_5' => null,
    ];

    #[DataProvider('provideVariables')]
    public function testGet(string $variable, ?string $value): void
    {
        $env = $this->getEnv();
        Assert::assertSame($value, $env->get($variable));
        if (is_null($value)) {
            $default = 'default';
            Assert::assertSame($default, $env->get($variable, $default));
        }
    }

    #[DataProvider('provideVariables')]
    public function testHas(string $variable, ?string $value): void
    {
        $env = $this->getEnv();
        Assert::assertSame(!is_null($value), $env->has($variable));
    }

    /**
     * @return array{0: string, 1: string|null}[]
     */
    public static function provideVariables(): iterable
    {
        foreach (self::ENV as $variable => $value) {
            yield [$variable, $value];
        }
    }

    private function getEnv(): Env
    {
        $env = [];
        foreach (self::ENV as $variable => $value) {
            if (!is_string($value)) {
                continue;
            }
            $env[$variable] = $value;
        }
        return new Env(new MemoryVariableProvider($env));
    }
}
