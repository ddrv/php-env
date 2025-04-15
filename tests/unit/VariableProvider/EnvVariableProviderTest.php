<?php

declare(strict_types=1);

namespace Tests\Unit\Ddrv\Env\VariableProvider;

use Ddrv\Env\VariableProvider\EnvVariableProvider;
use Ddrv\Env\VariableProvider\VariableProvider;

final class EnvVariableProviderTest extends VariableProviderTestCase
{
    /**
     * @inheritDoc
     */
    public static function provideDefined(): iterable
    {
        return [
            ['TEST_VAR_1', 'one'],
            ['TEST_VAR_2', 'two'],
            ['TEST_SOURCE', 'env'],
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provideUndefined(): iterable
    {
        return [
            ['TEST_VAR_3'],
            ['TEST_VAR_4'],
        ];
    }

    protected function createProvider(): VariableProvider
    {
        return new EnvVariableProvider();
    }
}
