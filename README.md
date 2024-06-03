# Trace

> 🔔 Subscribe to the [newsletter](https://chv.to/chevere-newsletter) to don't miss any update regarding Chevere.

![Chevere](chevere.svg)

[![Build](https://img.shields.io/github/actions/workflow/status/chevere/trace/test.yml?branch=2.0&style=flat-square)](https://github.com/chevere/trace/actions)
![Code size](https://img.shields.io/github/languages/code-size/chevere/trace?style=flat-square)
[![Apache-2.0](https://img.shields.io/github/license/chevere/trace?style=flat-square)](LICENSE)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%209-blueviolet?style=flat-square)](https://phpstan.org/)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fchevere%2Ftrace%2F2.0)](https://dashboard.stryker-mutator.io/reports/github.com/chevere/trace/2.0)

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=chevere_trace&metric=alert_status)](https://sonarcloud.io/dashboard?id=chevere_trace)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_trace&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=chevere_trace)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_trace&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=chevere_trace)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_trace&metric=security_rating)](https://sonarcloud.io/dashboard?id=chevere_trace)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=chevere_trace&metric=coverage)](https://sonarcloud.io/dashboard?id=chevere_trace)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=chevere_trace&metric=sqale_index)](https://sonarcloud.io/dashboard?id=chevere_trace)
[![CodeFactor](https://www.codefactor.io/repository/github/chevere/trace/badge)](https://www.codefactor.io/repository/github/chevere/trace)

## Summary

Tooling for handling [debug_backtrace](https://www.php.net/manual/en/function.debug-backtrace.php) items.

## Installing

Trace is available through [Packagist](https://packagist.org/packages/chevere/trace) and the repository source is at [chevere/trace](https://github.com/chevere/trace).

```sh
composer require chevere/trace
```

## Creating a Trace

Create a Trace by passing a PHP `debug_backtrace` array and a format object.

```php
use Chevere\Trace\Formats\PlainFormat;
use Chevere\Trace\Trace;

$debugBacktrace = debug_backtrace();
$format = new PlainFormat();
$trace = new Trace($debugBacktrace, $format);
```

## To Array

Use method `toArray` to get an array representation of the formatted trace.

```php
$array = $trace->toArray();
```

## To String

Use method `__toString` to get a string representation of the formatted trace.

```php
$string = $trace->__toString();
```

## Table

Use method `table` to get the array used to translate template keys to values.

```php
$table = $trace->table();
```

## Documentation

Documentation is available at [chevere.org](https://chevere.org/packages/trace).

## License

Copyright [Rodolfo Berrios A.](https://rodolfoberrios.com/)

Chevere is licensed under the Apache License, Version 2.0. See [LICENSE](LICENSE) for the full license text.

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
