<?php

declare(strict_types=1);

namespace Tests\Ddrv\Env\VariableProvider;

use Ddrv\Env\VariableProvider\VariableProvider;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

abstract class VariableProviderTestCase extends TestCase
{
    /**
     * @dataProvider provideDefined
     */
    final public function testGet(string $variable, ?string $value)
    {
        $provider = $this->createProvider();
        Assert::assertSame($value, $provider->get($variable));
    }

    /**
     * @dataProvider provideUndefined
     */
    final public function testGetUndefined(string $variable)
    {
        $provider = $this->createProvider();
        Assert::assertNull($provider->get($variable));
    }

    abstract public function provideDefined(): array;

    abstract public function provideUndefined(): array;

    abstract protected function createProvider(): VariableProvider;
}
