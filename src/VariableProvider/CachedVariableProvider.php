<?php

declare(strict_types=1);

namespace Ddrv\Env\VariableProvider;

final class CachedVariableProvider implements VariableProvider
{
    private VariableProvider $provider;
    /** @var array<string, string|null> */
    private array $cache = [];

    public function __construct(VariableProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @inheritDoc
     */
    public function get(string $variable): ?string
    {
        if (!array_key_exists($variable, $this->cache)) {
            $this->cache[$variable] = $this->provider->get($variable);
        }

        return $this->cache[$variable];
    }

    public function reload(): void
    {
        $this->cache = [];
    }
}
