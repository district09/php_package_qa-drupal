# Quality Assurance - Drupal

This package provides Quality Assurance tools and shared configuration for
Drupal websites and extensions (modules, themes, and profiles).

[![QA][qa-badge]][qa-link]
[![Maintainability][codeclimate-maint-badge]][codeclimate-maint-link]
[![License][license-badge]][license-link]

## Requirements and compatibility

- [Composer](https://getcomposer.org)
- PHP 8.3 or later
- Drupal 11.4 or later, or Drupal 12

| Drupal | PHP | Notes |
| --- | --- | --- |
| `^11.4` | `^8.3` | PHP 8.3 through 8.5 are supported. |
| `^12.0` | `^8.5` | Drupal 12 requires PHP 8.5. Behat is not currently available for this combination. |

QA Drupal 5.x intentionally drops Drupal 10 and PHP versions older than 8.3.
See [UPGRADE-5.x.md](UPGRADE-5.x.md) before updating an existing project.

QA Drupal 5.x uses [QA PHP 3.x](https://packagist.org/packages/district09/qa-php)
for its shared PHP QA toolchain. PHPUnit 11.5 is used with Drupal 11.4 and
PHPUnit 12.5 with Drupal 12.

## Installation

Installation depends on whether the consuming project is a Drupal website or a
Drupal extension.

### Drupal website

Add the `grumphp` entry to the `extra` section of the project's `composer.json`:

```json
"grumphp": {
    "config-default-path": "vendor/digipolisgent/qa-drupal/configs/grumphp-site.yml"
}
```

Install QA Drupal as a development dependency:

```bash
composer require --dev digipolisgent/qa-drupal:^5.0
```

### Drupal extension

Add the `grumphp` entry to the `extra` section of the extension's
`composer.json`:

```json
"grumphp": {
    "config-default-path": "vendor/digipolisgent/qa-drupal/configs/grumphp-extension.yml"
}
```

Install QA Drupal as a development dependency:

```bash
composer require --dev digipolisgent/qa-drupal:^5.0
```

## Behat support

Behat is optional in QA Drupal 5.x. It is no longer installed or run by the
default GrumPHP test suite. The shared Behat configuration, custom contexts,
and scaffold files remain available so Drupal 11 projects can opt in.

Drupal Extension 6.1 restricts its Symfony dependencies to Symfony 6 and 7,
while Drupal 12 requires Symfony 8. Installing both therefore creates an
unresolvable dependency conflict. Behat cannot be supported on Drupal 12 until
Drupal Extension publishes a Symfony 8 compatible release. Default Behat
support can be reconsidered once that upstream support exists.

Drupal 11 projects can enable the retained integration with:

```bash
composer require --dev --with-all-dependencies drupal/drupal-extension:^6.1
```

Then add the Behat task to the project's GrumPHP override:

```yaml
grumphp:
  testsuites:
    tests:
      tasks:
        - phpunit
        - behat
  tasks:
    behat:
      config: behat.qa-drupal.yml
```

Run GrumPHP once to generate `behat.qa-drupal.yml`. Copy
`vendor/digipolisgent/qa-drupal/scaffold/behat.local.yml.dist` to
`behat.local.yml` and adapt its base URL for the local Drupal site.

The Mink and browser-driver dependencies used by Drupal's PHPUnit browser tests
remain part of the package. Their presence does not enable Behat by itself.

## Configuration

### General

To extend or override a task's shared configuration, create the matching file
in the project root. For example, create `phpcs.xml` or `phpcs.local.xml` to
override the provided PHPCS configuration.

Use `.local.` files only for changes that should not be committed and exclude
them in `.gitignore`:

```gitignore
/*.local.*
```

YAML and NEON files extend the provided configuration by default. To omit one
configuration layer, add the following variable to `.env` or `.env.local`:

```dotenv
[FILENAME]_SKIP_[TYPE]=1
```

`[FILENAME]` is the task configuration filename and `[TYPE]` is one of:

- `LOCAL`: skip a file such as `phpstan.local.neon`.
- `PROJECT`: skip a file such as `phpstan.neon`.
- `PACKAGE_TYPE`: skip a provided file such as `phpstan-extension.neon` or
  `phpstan-site.neon`.
- `PACKAGE_GLOBAL`: skip a provided file such as `phpstan.neon`.

Other file types cannot be merged and the most specific available file is
copied instead.

### Composer security audit

The default pre-commit suite uses GrumPHP's native
`securitychecker_composeraudit` task. It audits the locked dependency versions
through Composer and replaces the removed Enlightn security checker.

### PHPStan in deprecations-only mode

Create `phpstan.neon` with the following contents to ignore everything except
deprecations:

```neon
parameters:
  customRulesetUsed: true
  ignoreErrors:
    - '#^(?:(?!deprecated).)*$#'
```

### Generated configuration files

Some GrumPHP tasks require a configuration file. QA Drupal creates these files
from its shared configuration and project-specific overrides. Files with a
`.qa-drupal.` suffix are generated in the project root and must not be
committed:

```gitignore
/*.qa-drupal.*
```

PHPUnit coverage reports and its cache should also be ignored:

```gitignore
/build
/.phpunit.cache
```

To run PHPUnit locally without coverage, copy `phpunit.qa-drupal.xml` to
`phpunit.local.xml` and remove the `<coverage>` section from the local copy.

## Run GrumPHP

GrumPHP runs the configured tasks on changed code during Git commit and push.
Run all tasks manually with:

```bash
vendor/bin/grumphp
```

Run selected tasks with:

```bash
vendor/bin/grumphp --tasks phpcs,phpmd
vendor/bin/grumphp --tasks phpunit
```

## PhpStorm

Run GrumPHP successfully at least once to generate the configuration files used
by PhpStorm:

- `phpcs.qa-drupal.xml`
- `phpmd.qa-drupal.xml`
- `phpunit.qa-drupal.xml`

Configure them under PHP quality tools and PHPUnit test-runner settings in the
IDE.

## Code Climate

Configure Code Climate to use the QA Drupal PHPCS rules:

```yaml
prepare:
  fetch:
    - url: "https://raw.githubusercontent.com/district09/php_package_qa-drupal/5.x/configs/phpcs-codeclimate.xml"
      path: ".phpcs.xml"

plugins:
  phpcodesniffer:
    enabled: true
    channel: beta
    config:
      standard: ".phpcs.xml"
```

### PHP compatibility

The PHPCompatibility rules can explicitly check the complete package PHP range:

```bash
php vendor/bin/phpcs -p --ignore="*/vendor/*" --extensions=php,inc,module,install,theme --runtime-set testVersion 8.3-8.5 --standard=PHPCompatibility ./web/modules/contrib
```

[license-badge]: https://img.shields.io/packagist/l/digipolisgent/qa-drupal
[license-link]: LICENSE.md

[qa-badge]: https://github.com/district09/php_package_qa-drupal/actions/workflows/qa.yml/badge.svg?branch=develop "QA build develop"
[qa-link]: https://github.com/district09/php_package_qa-drupal/actions/workflows/qa.yml

[codeclimate-maint-badge]: https://api.codeclimate.com/v1/badges/d3d6d20fbc6efb09337e/maintainability
[codeclimate-maint-link]: https://codeclimate.com/github/district09/php_package_qa-drupal/maintainability
