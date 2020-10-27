# ddrv/env

> Read variables from environment or .env file

# Install

```text
composer require ddrv/env:^1.0
```

# Usage

```text
# /path/to/project/.env

KEY_3=value3
```

```php
<?php

/*
 * $_ENV = [
 *     'PREFIX_KEY_1' => 'value1',
 *     'PREFIX_KEY_2' => 'value2',
 * ];
 */


$env = new Ddrv\Env\Env('/path/to/project/.env', 'PREFIX_');

$env->get('KEY_1'); // returns 'value1'
$env->get('KEY_2'); // returns 'value2'
$env->get('KEY_3'); // returns 'value3'
$env->get('KEY_4'); // returns null
$env->get('KEY_5', 'defaultValue'); // returns 'defaultValue'
```