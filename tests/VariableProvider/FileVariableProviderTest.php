<?php

declare(strict_types=1);

namespace Tests\Ddrv\Env\VariableProvider;

use Ddrv\Env\VariableProvider\FileVariableProvider;
use Ddrv\Env\VariableProvider\VariableProvider;

class FileVariableProviderTest extends VariableProviderTestCase
{
    public function provideDefined(): array
    {
        return [
            ['TEST_VAR_3', 'three'],
            ['TEST_VAR_4', 'four'],
            ['TEST_SOURCE', 'file'],
        ];
    }

    public function provideUndefined(): array
    {
        return [
            ['TEST_VAR_1'],
            ['TEST_VAR_2'],
            ['TEST_VAR_5'], // commented in file
        ];
    }

    protected function createProvider(): VariableProvider
    {
        return new FileVariableProvider(dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env.test');
    }
}
