This package provides a set of Quality Assurance tools and configuration files for
Drupal websites and extensions (modules, themes or profiles).


# Requirements

* [Composer](https://getcomposer.org)


# Versions

| Package | Drupal |
| ------- | ------ |
| 1       | 8      |


# Installation

First add one of following `grumphp` entries to the `extra` section of your `composer.json`.

For a website:

```json
"grumphp": {
    "config-default-path": "vendor/digipolisgent/qa-drupal/configs/grumphp-site.yml"
}
```

For an extension:

```json
"grumphp": {
    "config-default-path": "vendor/digipolisgent/qa-drupal/configs/grumphp-extension.yml"
}
```

Now install this package and its requirements by executing execute following command:
<pre><code>composer require --dev digipolisgent/qa-drupal</code></pre>


# Configuration

## General

If required you can extend or override the provided configuration file of a task.
Simply create the matching configuration file in the root of your project.

For example, to override the provided `phpcs.xml` file you can either create a
`phpcs.xml` or `phpcs.local.xml` file. Note that the latter one should only be
used for changes that shouldn't be comitted.

Yaml and Neon files will extend (merged into) the provided configuration file by default.
Create a `.env` or `.env.local` file and add following contents to change this behaviour:

```
[FILENAME]_SKIP_[TYPE]=1
```

Wherein `[FILENAME]` matches the configuration filename and `[TYPE]` is either:

- `LOCAL` to skip for example your `phpstan.local.neon` file.
- `PROJECT` to skip for example your `phpstan.neon` file.
- `PACKAGE_TYPE` to skip for example the provided `phpstan-extension.neon` or `phpstan-site.neon` file.
- `PACKAGE_GLOBAL` to skip for example the provided `phpstan.neon` file.

Other file types cannot be merged and will just override all other less specific files.


## PHPStan in deprecations only mode

Create a `phpstan.neon` file and add following contents to ignore everything except deprecations.

```
parameters:
  customRulesetUsed: true
  ignoreErrors:
    - '#^(?:(?!deprecated).)*$#'
```
