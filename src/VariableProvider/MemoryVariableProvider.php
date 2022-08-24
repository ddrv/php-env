<?php

declare(strict_types=1);

namespace Ddrv\Env\VariableProvider;

final class MemoryVariableProvider implements VariableProvider
{
    /**
     * @var string[]
     */
    private $env = [];

    public function __construct(array $variables)
    {
        foreach ($variables as $variable => $value) {
            if (!is_string($variable) || !is_string($value)) {
                continue;
            }
            $this->set($variable, $value);
        }
    }

    public function set(string $variable, string $value): void
    {
        $value = trim($value);
        if ($value === '') {
            $this->unset($variable);
            return;
        }
        $this->env[$variable] = $value;
    }

    public function unset(string $variable): void
    {
        if (!array_key_exists($variable, $this->env)) {
            return;
        }
        unset($this->env[$variable]);
    }

    /**
     * @inheritDoc
     */
    public function get(string $variable): ?string
    {
        return $this->env[$variable] ?? null;
    }
}
