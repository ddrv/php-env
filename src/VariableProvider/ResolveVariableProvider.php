<?php

declare(strict_types=1);

namespace Ddrv\Env\VariableProvider;

use Ddrv\Env\Exception\CyclicalDependencyDetected;
use Ddrv\Env\Exception\VariableUndefined;

final class ResolveVariableProvider implements VariableProvider
{
    private VariableProvider $provider;

    public function __construct(VariableProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @inheritDoc
     * @throws VariableUndefined
     */
    public function get(string $variable): ?string
    {
        $value = $this->provider->get($variable);
        if (is_null($value)) {
            return null;
        }

        do {
            $used = [$variable => true];
            $isResolved = $this->resolve($value, $used);
        } while ($isResolved);
        return str_replace(['\$', '\\\\'], ['$', '\\'], $value);
    }

    public function reload(): void
    {
        $this->provider->reload();
    }

    /**
     * @param array<string, bool> $used
     * @throws VariableUndefined
     */
    private function resolve(string &$value, array &$used): bool
    {
        $replacement = $this->find($value);
        if (is_null($replacement)) {
            return false;
        }

        $variable = $replacement['variable'];
        if (array_key_exists($variable, $used)) {
            $cycle = array_keys($used);
            $cycle[] = $variable;
            throw new CyclicalDependencyDetected($cycle);
        }

        $used[$variable] = true;
        $part = $this->getPart($used, $variable, $replacement['default'], $replacement['error']);
        $value = substr_replace($value, $part, $replacement['offset'], $replacement['length']);
        return true;
    }

    /**
     * @return array{offset: int, length: int, variable: string, default: string|null, error:string|null}|null
     */
    private function find(string $value): ?array
    {
        $offset = $this->findBegin($value);
        if (is_null($offset)) {
            return null;
        }
        $end = $this->findEnd($value, $offset);
        if (is_null($end)) {
            return null;
        }

        $length = $end - $offset + 1;
        $content = substr($value, $offset + 2, $length - 3);
        $array = explode(':', $content, 2);
        $variable = $array[0];
        if (str_contains($variable, ' ')) {
            return null;
        }

        $result = [
            'offset' => $offset,
            'length' => $length,
            'variable' => $variable,
            'default' => null,
            'error' => null,
        ];
        if (!array_key_exists(1, $array)) {
            return $result;
        }
        $additional = $array[1];
        if (str_starts_with($additional, '?')) {
            $error = trim(substr($additional, 1));
            $result['error'] = $error === '' ? null : $error;
            return $result;
        }

        if (!in_array(substr($additional, 0, 1), ['-', '='], true)) {
            return $result;
        }

        $default = trim(substr($additional, 1));
        $result['default'] = $default === '' ? null : $default;
        return $result;
    }

    private function findBegin(string $value, int $offset = 0): ?int
    {
        $offset = strpos($value, '${', $offset);
        if ($offset === false) {
            return null;
        }

        if ($this->isEscaped($value, $offset)) {
            return $this->findBegin($value, $offset + 2);
        }

        return $offset;
    }

    private function findEnd(string $value, int $offset): ?int
    {
        $offset = strpos($value, '}', $offset);
        if ($offset === false) {
            return null;
        }

        if ($this->isEscaped($value, $offset)) {
            return $this->findEnd($value, $offset + 1);
        }

        return $offset;
    }

    private function isEscaped(string $value, int $position): bool
    {
        $slashes = $this->countSlashes($value, $position);
        return $slashes % 2 === 1;
    }

    private function countSlashes(string $value, int $position): int
    {
        if ($position < 1) {
            return 0;
        }
        $slashes = 0;
        for ($number = $position - 1; $number >= 0; $number--) {
            if (substr($value, $number, 1) !== '\\') {
                break;
            }
            $slashes++;
        }
        return $slashes;
    }

    /**
     * @param array<string, bool> $used
     * @throws VariableUndefined
     */
    private function getPart(array &$used, string $variable, ?string $default, ?string $error): string
    {
        $value = $this->provider->get($variable);
        if (is_null($value) && !is_null($error)) {
            throw new VariableUndefined($variable, $error);
        }

        if (is_null($value)) {
            $value = $default ?? '';
        }

        do {
            $isResolved = $this->resolve($value, $used);
        } while ($isResolved);

        return $value;
    }
}
