<?php

declare(strict_types=1);

namespace Tests\Unit\Ddrv\Env\VariableProvider;

use Ddrv\Env\VariableProvider\FileVariableProvider;
use Ddrv\Env\VariableProvider\VariableProvider;

final class FileVariableProviderTest extends VariableProviderTestCase
{
    /**
     * @inheritDoc
     */
    public static function provideDefined(): iterable
    {
        return [
            ['TEST_VAR_3', 'three'],
            ['TEST_VAR_4', 'four'],
            ['TEST_SOURCE', 'file'],
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
            ['TEST_VAR_5'], // commented in file
        ];
    }

    protected function createProvider(): VariableProvider
    {
        return new FileVariableProvider(
            dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . '.env.test'
        );
    }
}
