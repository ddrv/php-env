<?php

declare(strict_types=1);

namespace Ddrv\Env\VariableProvider;

final class EnvVariableProvider implements VariableProvider
{
    /**
     * @inheritDoc
     */
    public function get(string $variable): ?string
    {
        $value = getenv($variable);
        if (is_string($value)) {
            return $value;
        }
        return null;
    }
}
