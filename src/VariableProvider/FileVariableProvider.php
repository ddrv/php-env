<?php

declare(strict_types=1);

namespace Ddrv\Env\VariableProvider;

use Ddrv\Env\Exception\SourceUnavailable;

final class FileVariableProvider implements VariableProvider
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var bool
     */
    private $read = false;

    /**
     * @var string[]
     */
    private $env = [];

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

            if (substr($value, 0, 1) === '"' && substr($value, -1) === '"') {
                $value = substr($value, 1, -1);
            }

            if ($value === '') {
                continue;
            }

            $this->env[$variable] = $value;
        }
        $this->read = true;
    }
}
