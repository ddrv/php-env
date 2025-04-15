<?php

declare(strict_types=1);

namespace Tests\Unit\Ddrv\Env\VariableProvider;

use Ddrv\Env\VariableProvider\VariableProvider;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

abstract class VariableProviderTestCase extends TestCase
{
    #[DataProvider('provideDefined')]
    final public function testGet(string $variable, ?string $value): void
    {
        $provider = $this->createProvider();
        Assert::assertSame($value, $provider->get($variable));
    }

    #[DataProvider('provideUndefined')]
    final public function testGetUndefined(string $variable): void
    {
        $provider = $this->createProvider();
        Assert::assertNull($provider->get($variable));
    }

    /**
     * @return array{0: string, 1: string|null}[]
     */
    abstract public static function provideDefined(): iterable;

    /**
     * @return array{0: string}[]
     */
    abstract public static function provideUndefined(): iterable;

    abstract protected function createProvider(): VariableProvider;
}
