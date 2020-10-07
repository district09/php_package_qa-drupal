This package provides a set of Quality Assurance tools and configuration files for
Drupal websites and extensions (modules, themes or profiles).


# Requirements

* [Composer](https://getcomposer.org)
* [Drupal](https://www.drupal.org/docs/develop/using-composer/using-composer-to-install-drupal-and-manage-dependencies) (Should be installed using composer. Note: for extensions Drupal should be installed in `../drupal`).


# Versions

| Package | Drupal |
| ------- | ------ |
| 1       | 8      |
| 2       | 9      |


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

Now install this package and its requirements by executing the following command:
<pre><code>composer require --dev digipolisgent/qa-drupal</code></pre>
