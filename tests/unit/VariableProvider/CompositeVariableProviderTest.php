<?php

declare(strict_types=1);

namespace Tests\Unit\Ddrv\Env\VariableProvider;

use Ddrv\Env\VariableProvider\CompositeVariableProvider;
use Ddrv\Env\VariableProvider\MemoryVariableProvider;
use Ddrv\Env\VariableProvider\VariableProvider;

final class CompositeVariableProviderTest extends VariableProviderTestCase
{
    /**
     * @inheritDoc
     */
    public static function provideDefined(): iterable
    {
        return [
            ['TEST_VAR_3', 'three'],
            ['TEST_VAR_4', 'four'],
            ['TEST_VAR_5', 'five'],
            ['TEST_VAR_6', 'six'],
            ['TEST_SOURCE', 'from_first'],
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
        ];
    }

    protected function createProvider(): VariableProvider
    {
        return new CompositeVariableProvider(
            new MemoryVariableProvider([
                'TEST_VAR_5' => 'five',
                'TEST_VAR_6' => 'six',
                'TEST_SOURCE' => 'from_first',
            ]),
            new MemoryVariableProvider([
                'TEST_VAR_3' => 'three',
                'TEST_VAR_4' => 'four',
                'TEST_SOURCE' => 'from_second',
            ]),
        );
    }
}
