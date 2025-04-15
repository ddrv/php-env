<?php

declare(strict_types=1);

namespace Tests\Unit\Ddrv\Env\VariableProvider;

use Ddrv\Env\VariableProvider\MemoryVariableProvider;
use Ddrv\Env\VariableProvider\VariableProvider;

final class MemoryVariableProviderTest extends VariableProviderTestCase
{
    /**
     * @inheritDoc
     */
    public static function provideDefined(): iterable
    {
        return [
            ['TEST_VAR_5', 'five'],
            ['TEST_VAR_6', 'six'],
            ['TEST_SOURCE', 'memory'],
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provideUndefined(): iterable
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
