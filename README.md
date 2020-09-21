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
    "config-default-path": "vendor/gent/qa-drupal/configs/grumphp-site.yml"
}
```

For an extension:

```json
"grumphp": {
    "config-default-path": "vendor/gent/qa-drupal/configs/grumphp-extension.yml"
}
```

Now install this package and its requirements by executing execute following command:
<pre><code>composer require --dev gent/qa-drupal:^1.0</code></pre>
