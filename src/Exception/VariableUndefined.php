<?php

declare(strict_types=1);

namespace Ddrv\Env\Exception;

use Exception;
use Throwable;

final class VariableUndefined extends Exception
{
    private ?string $error;

    public function __construct(string $variable, ?string $error = null, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            trim(sprintf('Variable %s undefined. %s', $variable, $error)),
            $code,
            $previous,
        );
        $this->error = $error;
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}
