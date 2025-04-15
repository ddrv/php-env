<?php

declare(strict_types=1);

namespace Ddrv\Env\Exception;

use RuntimeException;
use Throwable;

final class CyclicalDependencyDetected extends RuntimeException
{
    /** @var string[] */
    private array $cycle;

    /**
     * @param string[] $cycle
     */
    public function __construct(array $cycle, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Cyclical dependency detected (%s).', implode(' -> ', $cycle)),
            $code,
            $previous,
        );
        $this->cycle = $cycle;
    }

    /**
     * @return string[]
     */
    public function getCycle(): array
    {
        return $this->cycle;
    }
}
