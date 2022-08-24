<?php

declare(strict_types=1);

namespace Tests\Ddrv\Env;

use Ddrv\Env\Env;
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

    public function testFromFileCommented()
    {
        $env = $this->getEnv();
        Assert::assertSame(null, $env->get('TEST_VAR_5'));
    }

    public function testUndefined()
    {
        $env = $this->getEnv();
        Assert::assertSame(null, $env->get('TEST_VAR_6'));
        Assert::assertSame('default', $env->get('TEST_VAR_6', 'default'));
    }

    private function getEnv(): Env
    {
        return new Env(__DIR__ . DIRECTORY_SEPARATOR . '.env.test');
    }
}
