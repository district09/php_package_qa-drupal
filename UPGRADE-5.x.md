# Upgrading to QA Drupal 5.x

QA Drupal 5.x is the major-version compatibility release for Drupal 11.4 and
Drupal 12. Review every section before changing the Composer constraint in a
consumer project.

## Platform requirements

- Drupal 10 support is removed.
- PHP 8.3 is the minimum supported PHP version.
- Drupal 11.4 and later are supported on compatible PHP versions.
- Drupal 12 requires PHP 8.5.

Drupal 11.4 is the minimum API compatibility target, not a recommendation to
run an obsolete or insecure minor release. Production projects should always
use a currently supported Drupal release with all security updates applied.

Update the package with:

```bash
composer require --dev --with-all-dependencies digipolisgent/qa-drupal:^5.0
```

Resolve Drupal and PHP upgrades before installing QA Drupal 5.x if the project
still uses Drupal 10 or PHP 8.2 and earlier.

## Behat is optional

`drupal/drupal-extension` is no longer a mandatory dependency, and the Behat
task has been removed from the default GrumPHP `tests` suite. Existing Behat
configuration, QA Drupal contexts, and scaffold files remain in the package.

This change is required because Drupal Extension 6.1 supports Symfony 6 and 7,
whereas Drupal 12 requires Symfony 8. Composer cannot install those dependency
sets together. Until Drupal Extension provides Symfony 8 support, QA Drupal
cannot offer Behat on Drupal 12.

Drupal 11 consumers that still use Behat must install it explicitly:

```bash
composer require --dev --with-all-dependencies drupal/drupal-extension:^6.1
```

They must also opt the task back into a project GrumPHP override:

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

If a project already overrides the `tests` suite or `tasks.behat`, merge those
settings instead of replacing the entire project configuration. Drupal 12
projects must remove `drupal/drupal-extension`, `behat/behat`, and the Behat
GrumPHP task until an upstream Symfony 8 compatible release is available.

The retained Behat template is updated for Drupal Extension 6.1: `region_map`
is renamed to `regions`, and message selectors are nested below
`selectors.messages`. Apply the same changes to project-level Behat overrides.
The template supplies `http://localhost` as a safe default `base_url`; continue
to override it in `behat.local.yml` for the actual test site.

Mink, BrowserKit, Selenium, and WebDriver dependencies remain installed because
Drupal PHPUnit browser tests use them independently of Behat.

## Composer Audit replaces Enlightn

`enlightn/security-checker` is removed. The default pre-commit suite now uses
GrumPHP's native `securitychecker_composeraudit` task with the lock file as its
source.

Remove project overrides for `securitychecker_enlightn`. If the project copies
or extends the QA task list, replace them with:

```yaml
grumphp:
  tasks:
    securitychecker_composeraudit:
      locked: true
      run_always: false
```

## QA dependency generations

The 5.x dependency set builds on QA PHP 3.x and moves to Drupal- and
PHP-appropriate generations:

- Coder 9 and PHP_CodeSniffer 4 compatible rules.
- PHPCompatibility 10 alpha, required for PHP_CodeSniffer 4 and PHP 8.5 syntax.
- PHPStan Drupal 2 and PHPStan deprecation rules 2.
- PHPUnit 11.5 or 12.5, selected to match the Drupal line.
- Symfony PHPUnit Bridge 7.2 or 8.1.
- PHPCPD 8 or 9, allowing Composer to match PHPUnit 11 or 12.
- Current GrumPHP, Composer Normalize, TwigCS, PHP Mess Detector, Prophecy,
  Mink, and Drupal browser-driver generations.

The former custom PHPCPD VCS repository and global `minimum-stability: dev`
setting are removed. Projects relying on repository metadata inherited from
this package must declare their own repositories and stability policy.

When this library itself is the Composer root, Drupal's scaffold and Symfony
Runtime plugins are explicitly disabled so installing QA dependencies cannot
turn the package checkout into a Drupal site. Composer configuration from
dependencies is not inherited, so Drupal site consumers continue to control
and enable those plugins in their own root `composer.json`.

After updating, regenerate all `*.qa-drupal.*` task configuration files by
running GrumPHP.

## PHPUnit bootstrap and configuration

The extension bootstrap now follows Drupal's current test initialization:

- Drupal 11.4 uses its legacy deprecation-handler initialization.
- Drupal 12 uses `DeprecationHandler::preBootstrap()` with PHPUnit's parsed
  configuration.
- Composer autoload discovery, extension namespaces, `Drupal\TestSite`, the
  Drupal `DocumentElement` alias, locale, multibyte settings, and timezone are
  initialized consistently.
- Obsolete PHPUnit 8 compatibility mutation is removed.

The supplied site and extension XML configurations select an 11.5 or 12.5
schema for the installed PHPUnit major and use `.phpunit.cache`. Update
`.gitignore` if it still only ignores `.phpunit.result.cache`. Existing test
discovery and coverage output locations are retained.

## Continuous integration

This repository now uses `.github/workflows/qa.yml` instead of Travis CI. The
matrix covers Drupal 11.4 on PHP 8.3 through 8.5 and Drupal 12 on PHP 8.5.
Only the Drupal 11.4 PHP 8.3 job installs Drupal Extension and
loads the optional Behat configuration.
