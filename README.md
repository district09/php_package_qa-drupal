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

For an extension (to check only for D9 deprecations):

```json
"grumphp": {
    "config-default-path": "vendor/digipolisgent/qa-drupal/configs/grumphp-extension-d9.yml"
}
```

Now install this package and its requirements by executing execute following command:
<pre><code>composer require --dev digipolisgent/qa-drupal:^1.0</code></pre>


# Configuration

**This section only applies to the site and extension configuration of GrumPHP.**

You can optionally extend or override the task configuration files by creating
them in your project root. For example: create a `phpcs.xml` or `phpcs.local.xml`
file to override the configuration provided by this package.

Note that Yaml and Neon files will be merged with your local files. So if you create
a `phpstan.neon` file it will be merged with the `phpstan.neon` file of this package.

To prevent this, create a `.env` or `.env.local` file and add following contents:
```
PHPSTAN_SKIP_PACKAGE_GLOBAL=1
```

The skip variable name is always the same `[FILENAME]_SKIP_[TYPE]`, wherin `[FILENAME]`
if the file name and `[TYPE]` is either:

- `LOCAL` to skip `phpstan.local.neon`.
- `PROJECT` to skip `phpstan.neon`.
- `PACKAGE_TYPE` to skip `phpstan-extension.neon` (if in an extension) of this package.
- `PACKAGE_GLOBAL` to skip `phpstan.neon` of this package.
