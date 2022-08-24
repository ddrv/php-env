<?php

declare(strict_types=1);

namespace Ddrv\Env\VariableProvider;

final class PrefixedVariableProvider implements VariableProvider
{
    /**
     * @var VariableProvider
     */
    private $provider;

    /**
     * @var string
     */
    private $prefix;

    public function __construct(VariableProvider $provider, string $prefix)
    {
        $this->provider = $provider;
        $this->prefix = $prefix;
    }

    /**
     * @inheritDoc
     */
    public function get(string $variable): ?string
    {
        return $this->provider->get($this->prefix . $variable);
    }
}
