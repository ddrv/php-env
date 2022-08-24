<?php

declare(strict_types=1);

namespace Tests\Ddrv\Env;

use Ddrv\Env\Env;
use Ddrv\Env\VariableProvider\EnvVariableProvider;
use Ddrv\Env\VariableProvider\FileVariableProvider;
use Ddrv\Env\VariableProvider\MemoryVariableProvider;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase
{
    public function testFromEnv()
    {
        $env = $this->getEnv();
        Assert::assertSame('one', $env->get('TEST_VAR_1'));
        Assert::assertSame('two', $env->get('TEST_VAR_2'));
        Assert::assertSame('one', $env->get('TEST_VAR_1', 'default'));
        Assert::assertSame('two', $env->get('TEST_VAR_2', 'default'));
    }

    public function testFromFile()
    {
        $env = $this->getEnv();
        Assert::assertSame('three', $env->get('TEST_VAR_3'));
        Assert::assertSame('four', $env->get('TEST_VAR_4'));
        Assert::assertSame('three', $env->get('TEST_VAR_3', 'default'));
        Assert::assertSame('four', $env->get('TEST_VAR_4', 'default'));
    }

    public function testPriority()
    {
        $env = $this->getEnv();
        Assert::assertSame('env', $env->get('TEST_SOURCE'));
    }

    public function testUndefined()
    {
        $env = $this->getEnv();
        Assert::assertSame(null, $env->get('TEST_VAR_6'));
        Assert::assertSame('default', $env->get('TEST_VAR_6', 'default'));
    }

    public function testWithProvider()
    {
        $env = $this->getEnv();
        Assert::assertSame(null, $env->get('TEST_PHPUNIT'));
        $env = $env->withProvider(new MemoryVariableProvider(['TEST_PHPUNIT' => 'phpunit']));
        Assert::assertSame('phpunit', $env->get('TEST_PHPUNIT'));
    }

    private function getEnv(): Env
    {
        $first = new MemoryVariableProvider([
            'TEST_VAR_1' => 'one',
            'TEST_VAR_2' => 'two',
            'TEST_SOURCE' => 'env',
        ]);
        $second = new MemoryVariableProvider([
            'TEST_VAR_3' => 'three',
            'TEST_VAR_4' => 'four',
            'TEST_SOURCE' => 'file',
            ]);
        return new Env(
            $first,
            $second
        );
    }
}
