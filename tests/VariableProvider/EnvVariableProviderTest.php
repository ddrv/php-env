<?php

declare(strict_types=1);

namespace Tests\Ddrv\Env\VariableProvider;

use Ddrv\Env\VariableProvider\EnvVariableProvider;
use Ddrv\Env\VariableProvider\VariableProvider;

class EnvVariableProviderTest extends VariableProviderTestCase
{
    public function provideDefined(): array
    {
        return [
            ['TEST_VAR_1', 'one'],
            ['TEST_VAR_2', 'two'],
            ['TEST_SOURCE', 'env'],
        ];
    }

    public function provideUndefined(): array
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
