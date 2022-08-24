<?php

declare(strict_types=1);

namespace Tests\Ddrv\Env\VariableProvider;

use Ddrv\Env\VariableProvider\MemoryVariableProvider;
use Ddrv\Env\VariableProvider\PrefixedVariableProvider;
use Ddrv\Env\VariableProvider\VariableProvider;

class PrefixedVariableProviderTest extends VariableProviderTestCase
{
    public function provideDefined(): array
    {
        return [
            ['VAR_5', 'five'],
            ['VAR_6', 'six'],
            ['SOURCE', 'prefixed'],
        ];
    }

    public function provideUndefined(): array
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
