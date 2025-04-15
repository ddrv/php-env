<?php

declare(strict_types=1);

namespace Tests\Unit\Ddrv\Env\VariableProvider;

use Ddrv\Env\VariableProvider\CachedVariableProvider;
use Ddrv\Env\VariableProvider\VariableProvider;
use PHPUnit\Framework\Assert;

final class CachedVariableProviderTest extends VariableProviderTestCase
{
    private VariableProvider $baseVariableProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $env = [
            'TEST_VAR_5' => 'five',
            'TEST_VAR_6' => 'six',
        ];
        $this->baseVariableProvider = new class ($env) implements VariableProvider
        {
            /**
             * @var array<string, string>
             */
            private array $env;

            /**
             * @param array<string, string> $env
             */
            public function __construct(array $env)
            {
                $this->env = $env;
            }

            public function get(string $variable): ?string
            {
                return $this->env[$variable] ?? null;
            }

            public function reload(): void
            {
                $this->env = [];
            }
        };
    }

    /**
     * @inheritDoc
     */
    public static function provideDefined(): iterable
    {
        return [
            ['TEST_VAR_5', 'five'],
            ['TEST_VAR_6', 'six'],
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provideUndefined(): iterable
    {
        return [
            ['TEST_VAR_7'],
            ['TEST_VAR_8'],
        ];
    }

    public function testCache(): void
    {
        $provider = $this->createProvider();
        Assert::assertEquals('five', $provider->get('TEST_VAR_5'));

        $this->baseVariableProvider->reload();
        Assert::assertNull($this->baseVariableProvider->get('TEST_VAR_5'));

        Assert::assertEquals('five', $provider->get('TEST_VAR_5'));
        $provider->reload();
        Assert::assertNull($provider->get('TEST_VAR_5'));
    }

    protected function createProvider(): VariableProvider
    {
        return new CachedVariableProvider($this->baseVariableProvider);
    }
}
