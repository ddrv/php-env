<?php

declare(strict_types=1);

namespace Tests\Unit\Ddrv\Env\VariableProvider;

use Ddrv\Env\VariableProvider\MemoryVariableProvider;
use Ddrv\Env\VariableProvider\PrefixedVariableProvider;
use Ddrv\Env\VariableProvider\VariableProvider;

final class PrefixedVariableProviderTest extends VariableProviderTestCase
{
    /**
     * @inheritDoc
     */
    public static function provideDefined(): iterable
    {
        return [
            ['VAR_5', 'five'],
            ['VAR_6', 'six'],
            ['SOURCE', 'prefixed'],
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provideUndefined(): iterable
    {
        return [
            ['TEST_VAR_5'],
            ['TEST_VAR_6'],
            ['TEST_SOURCE'],
        ];
    }

    protected function createProvider(): VariableProvider
    {
        $variableProvider = new MemoryVariableProvider([
            'TEST_VAR_5' => 'five',
            'TEST_VAR_6' => 'six',
            'TEST_SOURCE' => 'prefixed',
        ]);
        return new PrefixedVariableProvider($variableProvider, 'TEST_');
    }
}
