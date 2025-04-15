<?php

declare(strict_types=1);

namespace Ddrv\Env\VariableProvider;

final class CompositeVariableProvider implements VariableProvider
{
    /**
     * @var VariableProvider[]
     */
    private array $providers;

    public function __construct(VariableProvider ...$providers)
    {
        $this->providers = $providers;
    }

    /**
     * @inheritDoc
     */
    public function get(string $variable): ?string
    {
        foreach ($this->providers as $provider) {
            $value = $provider->get($variable);
            if (is_string($value)) {
                return $value;
            }
        }

        return null;
    }

    public function reload(): void
    {
        foreach ($this->providers as $provider) {
            $provider->reload();
        }
    }
}
