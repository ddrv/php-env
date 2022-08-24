# ddrv/env

> Read variables from environment or .env file

# Install

```text
composer require ddrv/env:^2.0
```

# Usage

```text
# /path/to/project/.env

APP_VAR_3=value3
```

```php
<?php

use Ddrv\Env\Env;
use Ddrv\Env\VariableProvider\EnvVariableProvider;
use Ddrv\Env\VariableProvider\FileVariableProvider;

/*
 * $_ENV = [
 *     'APP_VAR_1' => 'value1',
 *     'APP_VAR_2' => 'value2',
 * ];
 */


$env = new Env(
    new EnvVariableProvider(),
    new FileVariableProvider('/path/to/project/.env'),
);

$env->get('APP_VAR_1'); // returns 'value1'
$env->get('APP_VAR_2'); // returns 'value2'
$env->get('APP_VAR_3'); // returns 'value3'
$env->get('APP_VAR_4'); // returns null
$env->get('APP_VAR_5', 'defaultValue'); // returns 'defaultValue'
```

## Prefixes

```php
<?php

use Ddrv\Env\Env;
use Ddrv\Env\VariableProvider\EnvVariableProvider;
use Ddrv\Env\VariableProvider\FileVariableProvider;
use Ddrv\Env\VariableProvider\PrefixedVariableProvider;

/*
 * $_ENV = [
 *     'APP_VAR_1' => 'value1',
 *     'APP_VAR_2' => 'value2',
 * ];
 */


$env = new Env(
    new PrefixedVariableProvider(new EnvVariableProvider(), 'APP_'),
    new PrefixedVariableProvider(new FileVariableProvider('/path/to/project/.env'), 'APP_'),
);

$env->get('VAR_1'); // returns 'value1'
$env->get('VAR_2'); // returns 'value2'
$env->get('VAR_3'); // returns 'value3'
$env->get('VAR_4'); // returns null
$env->get('VAR_5', 'defaultValue'); // returns 'defaultValue'
```
