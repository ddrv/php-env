<?php

declare(strict_types=1);

namespace Tests\Ddrv\Env\VariableProvider;

use Ddrv\Env\VariableProvider\MemoryVariableProvider;
use Ddrv\Env\VariableProvider\VariableProvider;

class MemoryVariableProviderTest extends VariableProviderTestCase
{
    public function provideDefined(): array
    {
        return [
            ['TEST_VAR_5', 'five'],
            ['TEST_VAR_6', 'six'],
            ['TEST_SOURCE', 'memory'],
        ];
    }

    public function provideUndefined(): array
    {
        return [
            ['TEST_VAR_1'],
            ['TEST_VAR_2'],
            ['TEST_VAR_3'],
            ['TEST_VAR_4'],
        ];
    }

    protected function createProvider(): VariableProvider
    {
        return new MemoryVariableProvider([
            'TEST_VAR_5' => 'five',
            'TEST_VAR_6' => 'six',
            'TEST_SOURCE' => 'memory',
        ]);
    }
}
