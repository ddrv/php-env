<?php

declare(strict_types=1);

namespace Ddrv\Env\VariableProvider;

use Ddrv\Env\Exception\SourceUnavailable;

final class FileVariableProvider implements VariableProvider
{
    private string $file;
    private bool $read = false;
    /** @var array<string, string> */
    private array $env = [];

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * @inheritDoc
     */
    public function get(string $variable): ?string
    {
        $this->parseFile();
        return $this->env[$variable] ?? null;
    }

    private function parseFile(): void
    {
        if ($this->read) {
            return;
        }

        if (!file_exists($this->file)) {
            throw new SourceUnavailable(sprintf('file %s not exists', $this->file));
        }
        if (!is_readable($this->file)) {
            throw new SourceUnavailable(sprintf('file %s not readable', $this->file));
        }

        $contents = file_get_contents($this->file);
        if (!is_string($contents)) {
            throw new SourceUnavailable(sprintf('file %s not readable', $this->file));
        }

        $contents = str_replace("\r", '', $contents);
        $lines = array_map('trim', explode("\n", $contents));
        foreach ($lines as $line) {
            $line = explode('#', $line)[0];
            if (!$line) {
                continue;
            }
            $arr = explode('=', $line, 2);
            if (count($arr) !== 2) {
                continue;
            }
            $variable = trim($arr[0]);
            $value = trim($arr[1]);

            if (str_starts_with($value, '"') && str_ends_with($value, '"')) {
                $value = substr($value, 1, -1);
            }

            if ($value === '') {
                continue;
            }

            $this->env[$variable] = $value;
        }
        $this->read = true;
    }

    public function reload(): void
    {
        $this->read = false;
        $this->env = [];
    }
}
