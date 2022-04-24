<?php

declare(strict_types=1);

namespace Ddrv\Env;

class Env
{

    /**
     * @var string
     */
    private $prefix;

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

    public function __construct(string $file, string $prefix = '')
    {
        $this->file = $file;
        $this->prefix = trim($prefix);
    }

    public function get(string $env, ?string $default = null): ?string
    {
        if (array_key_exists($env, $this->env)) {
            return $this->env[$env];
        }
        $key = $this->prefix . $env;
        $value = getenv($key);
        if ($value) {
            $this->env[$env] = $value;
            return $value;
        }
        if (!$this->read) {
            if (!file_exists($this->file)) {
                return $default;
            }
            $contents = file_get_contents($this->file);
            $contents = str_replace("\r", '', $contents);
            $lines = array_map('trim', explode("\n", $contents));
            foreach ($lines as $line) {
                $line = explode('#', $line)[0];
                if (!$line) {
                    continue;
                }
                if ($this->prefix !== '' && strpos($line, $this->prefix) !== 0) {
                    continue;
                }
                $arr = explode('=', $line, 2);
                if (count($arr) !== 2) {
                    continue;
                }
                $var = trim($arr[0]);
                $system = getenv($var);
                if ($system) {
                    $this->env[$var] = $system;
                    continue;
                }
                $var = substr($var, strlen($this->prefix));
                $value = trim($arr[1]);
                if (substr($value, 0, 1) === '"' && substr($value, -1) === '"') {
                    $value = substr($value, 1, -1);
                }
                $this->env[$var] = $value;
            }
            $this->read = true;
        }
        if (array_key_exists($env, $this->env)) {
            return $this->env[$env];
        }
        return $default;
    }
}
