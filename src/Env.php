<?php

declare(strict_types=1);

namespace Ddrv\Env;

use Ddrv\Env\VariableProvider\VariableProvider;

final class Env
{
    /**
     * @var VariableProvider[]
     */
    private $providers;

    public function __construct(VariableProvider ...$providers)
    {
        $this->providers = $providers;
    }

    public function withProvider(VariableProvider $provider): self
    {
        $that = clone $this;
        $that->providers[] = $provider;
        return $that;
    }

    public function get(string $variable, ?string $default = null): ?string
    {
        foreach ($this->providers as $provider) {
            $value = $provider->get($variable);
            if (is_string($value)) {
                return $value;
            }
        }
        return $default;
    }
}
