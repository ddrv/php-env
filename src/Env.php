<?php

declare(strict_types=1);

namespace Ddrv\Env;

use Ddrv\Env\Exception\VariableUndefined;
use Ddrv\Env\Variable\Variable;
use Ddrv\Env\VariableProvider\VariableProvider;

final class Env
{
    private VariableProvider $provider;

    public function __construct(VariableProvider $variableProvider)
    {
        $this->provider = $variableProvider;
    }

    public function optional(string $variable): ?Variable
    {
        $value = $this->provider->get($variable);
        if (is_null($value)) {
            return null;
        }
        return new Variable($value);
    }

    /**
     * @throws VariableUndefined
     */
    public function required(string $variable): Variable
    {
        $value = $this->provider->get($variable);
        if (is_null($value)) {
            throw new VariableUndefined($variable);
        }
        return new Variable($value);
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
