<?php

declare(strict_types=1);

namespace Ddrv\Env;

use Ddrv\Env\VariableProvider\VariableProvider;

final class Env
{
    private VariableProvider $provider;

    public function __construct(VariableProvider $variableProvider)
    {
        $this->provider = $variableProvider;
    }

    public function get(string $variable, ?string $default = null): ?string
    {
        return $this->provider->get($variable) ?? $default;
    }

    public function has(string $variable): bool
    {
        return !is_null($this->provider->get($variable));
    }

    public function reload(): void
    {
        $this->provider->reload();
    }
}
