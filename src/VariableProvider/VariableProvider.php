<?php

declare(strict_types=1);

namespace Ddrv\Env\VariableProvider;

use Ddrv\Env\Exception\SourceUnavailable;

interface VariableProvider
{
    /**
     * @throws SourceUnavailable
     */
    public function get(string $variable): ?string;
}
