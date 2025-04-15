<?php

declare(strict_types=1);

namespace Tests\Unit\Ddrv\Env\VariableProvider;

use Ddrv\Env\Exception\CyclicalDependencyDetected;
use Ddrv\Env\Exception\VariableUndefined;
use Ddrv\Env\VariableProvider\MemoryVariableProvider;
use Ddrv\Env\VariableProvider\ResolveVariableProvider;
use Ddrv\Env\VariableProvider\VariableProvider;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Throwable;

final class ResolveVariableProviderTest extends VariableProviderTestCase
{
    public function testRequired(): void
    {
        $provider = $this->createProvider();
        try {
            $provider->get('TEST_VAR_DEPENDENCY_REQUIRED');
        } catch (Throwable $exception) {
            Assert::assertInstanceOf(VariableUndefined::class, $exception);
            Assert::assertEquals('Required.', $exception->getError());
            Assert::assertEquals('Variable TEXT_VAR_PART_9 undefined. Required.', $exception->getMessage());
        }
    }

    /**
     * @param string[] $cycle
     */
    #[DataProvider('provideCycled')]
    public function testCycled(string $variable, array $cycle): void
    {
        $provider = $this->createProvider();
        try {
            $provider->get($variable);
            $this->fail(
                sprintf('Failed asserting that exception of type "%s" is thrown.', CyclicalDependencyDetected::class)
            );
        } catch (Throwable $exception) {
            Assert::assertInstanceOf(CyclicalDependencyDetected::class, $exception);
            Assert::assertEquals($cycle, $exception->getCycle());
            Assert::assertEquals(
                sprintf('Cyclical dependency detected (%s).', implode(' -> ', $cycle)),
                $exception->getMessage()
            );
        }
    }

    /**
     * @inheritDoc
     */
    public static function provideDefined(): iterable
    {
        return [
            ['DEPENDENCY', 'dependency'],
            ['TEXT_VAR_PART', 'ue'],
            ['TEST_VAR_SIMPLE', 'simple'],
            ['TEST_VAR_COMPLEX', 'value with dependency'],
            ['TEST_VAR_DEPENDENCY_NON_EXISTENT', 'val'],
            ['TEST_VAR_DEPENDENCY_OPTIONAL_1', 'value'],
            ['TEST_VAR_DEPENDENCY_OPTIONAL_2', 'value'],
            ['TEST_VAR_DEPENDENCY_ESCAPED', 'val${DEPENDENCY}'],
            ['TEST_VAR_DEPENDENCY_UNESCAPED', 'val\\dependency'],
        ];
    }

    /**
     * @inheritDoc
     */
    public static function provideUndefined(): iterable
    {
        return [
            ['TEXT_VAR_PART_9'],
        ];
    }

    /**
     * @return array{0: string, 1: string[]}[]
     */
    public static function provideCycled(): iterable
    {
        return [
            ['TEST_VAR_CYCLED', ['TEST_VAR_CYCLED', 'CYCLED_1', 'CYCLED_2', 'CYCLED_3', 'CYCLED_1']],
            ['CYCLED_4', ['CYCLED_4', 'CYCLED_4']],
        ];
    }

    protected function createProvider(): VariableProvider
    {
        $variableProvider = new MemoryVariableProvider([
            'DEPENDENCY' => 'dependency',
            'TEXT_VAR_PART' => 'ue',
            'TEST_VAR_SIMPLE' => 'simple',
            'TEST_VAR_COMPLEX' => 'val${TEXT_VAR_PART} with ${DEPENDENCY}',
            'TEST_VAR_DEPENDENCY_NON_EXISTENT' => 'val${TEXT_VAR_PART_9}',
            'TEST_VAR_DEPENDENCY_OPTIONAL_1' => 'val${TEXT_VAR_PART_9:-ue}',
            'TEST_VAR_DEPENDENCY_OPTIONAL_2' => 'val${TEXT_VAR_PART_9:=ue}',
            'TEST_VAR_DEPENDENCY_ESCAPED' => 'val\${DEPENDENCY}',
            'TEST_VAR_DEPENDENCY_UNESCAPED' => 'val\\\\${DEPENDENCY}',
            'TEST_VAR_DEPENDENCY_REQUIRED' => 'val${TEXT_VAR_PART_9:?Required.}',
            'TEST_VAR_CYCLED' => 'val${CYCLED_1}',
            'CYCLED_1' => '${CYCLED_2}',
            'CYCLED_2' => '${CYCLED_3}',
            'CYCLED_3' => '${CYCLED_1}',
            'CYCLED_4' => '${CYCLED_4}',
        ]);
        return new ResolveVariableProvider($variableProvider);
    }
}
