# Changelog

All notable changes to ddrv/env are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.0.0] - 2025-04-15

### Added

- Changelog file;
- `\Ddrv\Env\VariableProvider\VariableProvider::reload()` method;
- `\Ddrv\Env\Env::has()` method for check variable;
- `\Ddrv\Env\Env::reload()` method for reset cached values;
- `\Ddrv\Env\VariableProvider\CompositeVariableProvider` provider for using many providers as single;
- `\Ddrv\Env\VariableProvider\ResolveVariableProvider` provider for resolving internal variables;
- `\Ddrv\Env\VariableProvider\CachedVariableProvider` provider for caching variables in memory;
- type cast support (see [README](./README.md)).

### Changed

- All not classes finalized;
- `\Ddrv\Env\Env::__construct()` method takes single variable provider (Use `\Ddrv\Env\VariableProvider\CompositeVariableProvider` for old behavior).

### Removed

- PHP 7 and 8.0 supports.
- `\Ddrv\Env\Env::withProvider()` method
- `\Ddrv\Env\VariableProvider\MemoryVariableProvider::set()` method
- `\Ddrv\Env\VariableProvider\MemoryVariableProvider::unset()` method