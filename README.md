# ddrv/env

> Read variables from environment or .env file

# Install

```text
composer require ddrv/env:^3.0
```

# Usage

For example, we have a global array

```php
$_ENV = [
    'APP_VAR_1' => 'value1',
    'APP_VAR_2' => 'value2',
    'APP_VAR_BOOL_TRUE' => 'true',
    'APP_VAR_BOOL_FALSE' => '0',
    'APP_VAR_INT_ONE' => '1',
    'APP_VAR_FLOAT_PI' => '3.1415',
    'APP_VAR_INT_ENUM_ONE' => '1',
    'APP_VAR_STRING_ENUM_A' => 'a',
    'APP_VAR_ENUM_FOO' => 'Foo',
    'APP_VAR_ENUM_STRING_A' => 'A',
    'APP_VAR_ENUM_INT_1' => 'One',
];
```

and dotenv file /path/to/project/.env

```text
# /path/to/project/.env

APP_VAR_3=value3
```

## System environment

```php
<?php

use Ddrv\Env\Env;
use Ddrv\Env\VariableProvider\EnvVariableProvider;
use Ddrv\Env\Exception\VariableUndefined;
use Tests\Code\Ddrv\Env\IntEnum;
use Tests\Code\Ddrv\Env\StringEnum;
use Tests\Code\Ddrv\Env\UntypedEnum;

$env = new Env(new EnvVariableProvider());

(string)$env->optional('APP_VAR_1'); // returns 'value1'
(string)$env->optional('APP_VAR_2'); // returns 'value2'
(string)$env->optional('APP_VAR_3'); // returns null because $_ENV has not 'APP_VAR_3' key

(string)$env->required('APP_VAR_1'); // returns 'value1'
(string)$env->required('APP_VAR_2'); // returns 'value2'
$env->required('APP_VAR_3'); // throws VariableUndefined exception because $_ENV has not 'APP_VAR_3' key

/** Cast types */
$env->optional('APP_VAR_BOOL_FALSE')->bool(); // returns false (allowed strings: 'false', 'off', '0', 'no', '')
$env->required('APP_VAR_BOOL_TRUE')->bool(); // returns true (allowed strings: 'true', 'on', '1', 'yes')
$env->required('APP_VAR_INT_ONE')->int(); // returns 1
$env->required('APP_VAR_FLOAT_PI')->int(); // returns 3.1415
$env->required('APP_VAR_FLOAT_PI')->string(); // returns "3.1415"
$env->required('APP_VAR_FLOAT_PI')->__toString(); // returns "3.1415"

$env->required('APP_VAR_INT_ENUM_ONE')->enum(IntEnum::class); // returns IntEnum::One
$env->required('APP_VAR_STRING_ENUM_A')->enum(StringEnum::class); // returns StringEnum::A

$env->required('APP_VAR_ENUM_FOO')->enumByName(UntypedEnum::class); // returns UntypedEnum::Foo
$env->required('APP_VAR_ENUM_STRING_A')->enumByName(StringEnum::class); // returns StringEnum::A
$env->required('APP_VAR_ENUM_INT_1')->enumByName(IntEnum::class); // returns IntEnum::One

/** Default value for optional */
$env->optional('APP_VAR_3')?->string() ?? 'default'; // returns "default"
```

## dotenv file

```php
<?php

use Ddrv\Env\Env;
use Ddrv\Env\VariableProvider\FileVariableProvider;

$env = new Env(new FileVariableProvider('/path/to/project/.env'));

$env->optional('APP_VAR_1'); // returns null because APP_VAR_3 not defined in /path/to/project/.env file
(string)$env->optional('APP_VAR_3'); // returns 'value3'
```

## Memory

```php
<?php

use Ddrv\Env\Env;
use Ddrv\Env\VariableProvider\MemoryVariableProvider;

$variableProvider = new MemoryVariableProvider([
    'APP_VAR_4' => 'value4',
])
$env = new Env($variableProvider);

(string)$env->optional('APP_VAR_4'); // returns 'value4'
$env->optional('APP_VAR_5'); // returns null
$variableProvider->set('APP_VAR_5', 'value5');
(string)$env->optional('APP_VAR_5'); // returns 'value5'
```

## Prefixes

```php
<?php

use Ddrv\Env\Env;
use Ddrv\Env\VariableProvider\EnvVariableProvider;
use Ddrv\Env\VariableProvider\PrefixedVariableProvider;

$env = new Env(new PrefixedVariableProvider(new EnvVariableProvider(), 'APP_'));

(string)$env->optional('VAR_1'); // returns 'value1'
$env->optional('VAR_3'); // returns null
```

## Resolving variables

```php
<?php

use Ddrv\Env\Env;
use Ddrv\Env\VariableProvider\EnvVariableProvider;
use Ddrv\Env\VariableProvider\MemoryVariableProvider;

$env = new Env(new ResolveVariableProvider(new MemoryVariableProvider([
    'SCHEME_1' => 'http',
    'PATH_1' => '/path/to/file',
    'HOST_1' => '127.0.0.1',
    'PORT_1' => '8080',
    'TOKEN_1' => 'secret',
    'CYCLED_1' => '${CYCLED_2}',
    'CYCLED_2' => '${CYCLED_3}',
    'CYCLED_3' => '${CYCLED_1}',
    'URL_1' => '${SCHEME_1}://${HOST_1}:${PORT_1}${PATH_1}',
    'URL_2' => '${SCHEME_2:-https}://${HOST_2:-localhost}:${PORT_2:-1080}${PATH_1:-/}',
    'URL_3' => 'http://localhost:1080/api/version?token=\${TOKEN_1}',
    'URL_4' => 'http://localhost:1080/api/version?token=\\\\${TOKEN_1}',
    'URL_5' => 'http://localhost:1080/api/version?token=${TOKEN_2:?Token is required.}',
    'URL_6' => 'http://localhost:1080/${CYCLED_1}',
])));

(string)$env->optional('URL_1'); // returns 'http://127.0.0.1:8080/path/to/file'
(string)$env->optional('URL_2'); // returns 'https://localhost:1080/'
(string)$env->optional('URL_3'); // returns 'http://localhost:1080/api/version?token=${TOKEN_1}'
(string)$env->optional('URL_4'); // returns 'http://localhost:1080/api/version?token=\secret'
$env->optional('URL_5'); // throws Ddrv\Env\Exception\VariableUndefined with message 'Variable TOKEN_2 undefined. Token is required.'
$env->optional('URL_6'); // throws \Ddrv\Env\Exception\CyclicalDependencyDetected with message 'Cyclical dependency detected (URL_6 -> CYCLED_1 -> CYCLED_2 -> CYCLED_3 -> CYCLED_1).'
```

## Composite

```php
<?php

use Ddrv\Env\Env;
use Ddrv\Env\VariableProvider\CompositeVariableProvider;
use Ddrv\Env\VariableProvider\EnvVariableProvider;
use Ddrv\Env\VariableProvider\FileVariableProvider;
use Ddrv\Env\VariableProvider\MemoryVariableProvider;

$env = new Env(new CompositeVariableProvider(
    new EnvVariableProvider(),
    new FileVariableProvider('/path/to/project/.env'),
    new MemoryVariableProvider([
        'APP_VAR_1' => 'other1',
        'APP_VAR_2' => 'other2',
        'APP_VAR_3' => 'other3',
        'APP_VAR_4' => 'other4',
    ]),
));

(string)$env->optional('APP_VAR_1'); // returns 'value1' because $_ENV has 'APP_VAR_1' key and priority of EnvVariableProvider is higher than that of MemoryVariableProvider
(string)$env->optional('APP_VAR_2'); // returns 'value2' because $_ENV has 'APP_VAR_2' key and priority of EnvVariableProvider is higher than that of MemoryVariableProvider
(string)$env->optional('APP_VAR_3'); // returns 'value3' because APP_VAR_3 defined in /path/to/project/.env file and priority of FileVariableProvider is higher than that of MemoryVariableProvider
(string)$env->optional('APP_VAR_4'); // returns 'other4' because MemoryVariableProvider has 'APP_VAR_4' variable and $_ENV has not 'APP_VAR_4' key and APP_VAR_4 not defined in /path/to/project/.env file
$env->optional('APP_VAR_5'); // returns null because none of the providers contain the variable APP_VAR_5
```

## Caching

```php
<?php

use Ddrv\Env\Env;
use Ddrv\Env\VariableProvider\CachedVariableProvider;
use Ddrv\Env\VariableProvider\EnvVariableProvider;

$env = new Env(new CachedVariableProvider(
    new EnvVariableProvider(),
));

(string)$env->optional('APP_VAR_1'); // returns 'value1' because $_ENV has 'APP_VAR_1' key and priority of EnvVariableProvider is higher than that of MemoryVariableProvider

putenv('APP_VAR_1=');
(string)$env->optional('APP_VAR_1'); // returns 'value1' because CachedVariableProvider cache this value.
$env->reload();
(string)$env->optional('APP_VAR_1'); // returns '' because reload() method clear cache.
```
