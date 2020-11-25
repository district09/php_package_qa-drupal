# Quality Assurance - Drupal

This package provides a set of Quality Assurance tools and configuration files
for Drupal websites and extensions (modules, themes or profiles).

## Requirements

* [Composer](https://getcomposer.org)

## Versions

| Package | Drupal |
| ------- | ------ |
| 1       | 8      |

## Installation

The installation depends on the type of project: website or Drupal module.

### Drupal website

Add the `grumphp` entry to the `extra` section of your `composer.json`.

```json
"grumphp": {
    "config-default-path": "vendor/digipolisgent/qa-drupal/configs/grumphp-site.yml"
}
```

Add the qa-drupal package as dev requirement:

```bash
composer require --dev digipolisgent/qa-drupal:^1.0
```

### Drupal module

Add the `grumphp` entry to the `extra` section of your `composer.json`.

```json
"grumphp": {
    "config-default-path": "vendor/digipolisgent/qa-drupal/configs/grumphp-extension.yml"
}
```

Add the qa-drupal package as dev requirement:

```bash
composer require --dev digipolisgent/qa-drupal:^1.0
```

**NOTE** : phpstan needs a Drupal project in a `drupal` directory next to the
module directory. You can do this by running following command in the directory
above the module directory:

```bash
composer create-project "drupal/recommended-project:8.9.x-dev" ./drupal --prefer-dist
```

You can replace the drupal version by 8.8.x-dev, 9.0.x-dev, 9.1.x-dev, ...

## Configuration

### General

If required you can extend or override the provided configuration file of a
task. Simply create the matching configuration file in the root of your project.

For example, to override the provided `phpcs.xml` file you can either create a
`phpcs.xml` or `phpcs.local.xml` file.

Note that the `.local.` files should only be used for changes that shouldn't be
committed. Exclude them in `.gitignore`:

```gitignore
/*.local.*
```

Yaml and Neon files will extend (merged into) the provided configuration file by
default. Create a `.env` or `.env.local` file and add following contents to
change this behaviour:

```
[FILENAME]_SKIP_[TYPE]=1
```

Wherein `[FILENAME]` matches the configuration filename and `[TYPE]` is either:

- `LOCAL` to skip for example your `phpstan.local.neon` file.
- `PROJECT` to skip for example your `phpstan.neon` file.
- `PACKAGE_TYPE` to skip for example the provided `phpstan-extension.neon` or
  `phpstan-site.neon` file.
- `PACKAGE_GLOBAL` to skip for example the provided `phpstan.neon` file.

Other file types cannot be merged and will just override all other less specific
files.

### PHPStan in deprecations only mode

Create a `phpstan.neon` file and add following contents to ignore everything
except deprecations:

```
parameters:
  customRulesetUsed: true
  ignoreErrors:
    - '#^(?:(?!deprecated).)*$#'
```

### Ignore automatically created config files

Some GrumPHP tasks require a config file. These are automatically creacted, from
the examples within vendor/qa-drupal/config or by the project specific files
within your website or drupal module root directory. The generated files are
also stored in the same website/module root. You can recognize these files by
the `.qa-drupal.` suffix.

**These files should not be committed!** Add them to the `.gitignore` file:

```gitignore
/*.qa-drupal.*
```

### Ignore PHPUnit build files

When the PHPUnit task runs, coverage report files are stored into the `build`
directory located in the root of your website/project. Add this file to the
`.gitignore` file:

```gitignore
/build
/.phpunit.result.cache
```

### Run PHPUnit locally without coverage

Running PHPUnit with coverage report is time consuming. You can locally speed up
PHPUnit by copying the generated `phpunit.qa-drupal.xml` file to
`phpunit.local.xml` and remove the `<logging>` section from it.

## Run GrumPHP

GrumPHP will automatically run all tasks on the changed code on git commit and
push.

You can run all tasks at once:

```bash
vendor/bin/grumphp
```

Or you can run one or more specific tasks manually by running:

```bash
vendor/bin/grumphp --tasks phpcs,phpmd
vendor/bin/grumphp --tasks phpunit
```

## PHPStorm

PHPStorm requires config files for PHP_CodeSniffer, PHP Mess Detector & PhpUnit.
Run the grumphp command at least once (successfully) to generate these files.

The files will be created as:

- `phpcs.qa-drupal.xml` : PHP_CodeSniffer config file.
- `phpmd.qa-drupal.xml` : PHP Mess Detector config file.
- `phpunit.qa-drupal.xml` : PHPUnit config file.

Use these files in the PHPStorm configuration.
