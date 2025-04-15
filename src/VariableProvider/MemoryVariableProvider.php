<?php

declare(strict_types=1);

namespace Ddrv\Env\VariableProvider;

final class MemoryVariableProvider implements VariableProvider
{
    /** @var string[] */
    private array $env = [];

    /**
     * @param array<array-key, mixed> $variables
     */
    public function __construct(array $variables)
    {
        foreach ($variables as $variable => $value) {
            if (!is_string($variable) || !is_string($value)) {
                continue;
            }

            $value = trim($value);
            if ($value === '') {
                continue;
            }

            $this->env[$variable] = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function get(string $variable): ?string
    {
        return $this->env[$variable] ?? null;
    }

    public function reload(): void
    {
    }
}
