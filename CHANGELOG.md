# Changelog

All Notable changes to the **Quality Assurance - Drupal** package.

## [5.0.0]

### Added

- Added Drupal 12 support and a compatibility matrix for Drupal 11/12 with PHP
  8.3 through 8.5.
- Added dual-path PHPUnit bootstrap support for Drupal 11.1's legacy
  deprecation initialization and Drupal 12's `preBootstrap()` flow.
- Added Composer autoload, extension namespace, test-site, document-element,
  locale, and multibyte initialization to the extension bootstrap.
- Added focused tests for configuration merging, skip variables, and
  transactional filesystem commit/rollback behavior.
- Added a GitHub Actions matrix for Drupal 11.4 on PHP 8.3 through 8.5 and
  Drupal 12 on PHP 8.5.
- Added Lennart Van Vaerenbergh as a package author.

### Changed

- Raised the platform requirements to PHP `^8.3` and Drupal
  `^11.4 || ^12.0`.
- Use QA PHP `^3.0` for shared QA tooling. PHPUnit 10 support is removed;
  PHPUnit 11.5 and 12.5 are selected for Drupal 11.4 and Drupal 12.
- Updated Coder, PHPCompatibility, PHPStan, PHPUnit, Symfony PHPUnit Bridge,
  PHPCPD, GrumPHP, and related QA tools for Drupal 11/12 and PHP 8.3/8.5.
- Allowed the PHPMD 3 and PDepend 3 development lines specifically for Drupal
  12 because the stable PHPMD 2/PDepend 2 stack does not support Symfony 8.
- Replaced Enlightn Security Checker with GrumPHP's native Composer Audit task.
- Made Behat optional and removed it from the default test suite. Drupal 11
  consumers can opt in with Drupal Extension `^6.1`.
- Updated the retained Behat template for Drupal Extension 6.1's `regions` and
  nested message-selector configuration.
- Updated PHPUnit XML configuration for current PHPUnit conventions while
  preserving test discovery and coverage output.
- Replaced Travis CI documentation and configuration with GitHub Actions.
- Disabled Drupal scaffold and Symfony Runtime plugin execution when this
  library is installed as the Composer root; consumer root configuration is
  unaffected.

### Removed

- Removed Drupal 10 and PHP versions older than 8.3 from the supported matrix.
- Removed Drupal Extension from mandatory dependencies. It currently supports
  Symfony 6/7, while Drupal 12 requires Symfony 8, so Behat cannot be supported
  on Drupal 12 until an upstream compatible release is available.
- Removed `enlightn/security-checker`, the custom PHPCPD VCS repository, the
  global development stability setting, and obsolete PHPUnit 8 bootstrap
  compatibility code.

## [4.1.4]

### Fixed

- #70: Fix phpunit coverage build directory.

## [4.1.3]

### Removed

- Removed the unnecessary paths since we are using wildcard.

## [4.1.2]

### Added

- Add exclude to testsuite.

### Removed

- Removed vendor map from source config.

## [4.1.1]

### Added

- Add vendor map to phpunit-extension ignore.

## [4.1.0]

### Added

- Add support for Drupal 10.x so it can be used on PHP 8.4 platforms.
- Add support for PHPUnit 11.x & 12.x.
- Add Symfony 7 support to symfony/phpunit-bridge.

### Updated

- Update GrumPHP to 2.x to fix PHP 8.4 deprecations.
- Update PHPStan to 2.x.

## [4.0.0]

### Added

- Add support for modules who don't have submodules grouped in the modules dir.

## [4.0.0-alpha6]

### Updated

- Update phpunit config file for Drupal extensions for PHPUnit 10.5+.
- Update phpunit config file for Drupal sites for PHPUnit 10.5+.

## [4.0.0-alpha5]

### Fixed

- Phpunit not working for extensions (modules)

## [4.0.0-alpha4]

### Added

- Added friendsoftwig/twigcs again since this is D11 compatible.

### Changed

- Since Drupal 11 we don't need testsuites anymore and we just need to define the folder where the specific tests are in.
  See https://git.drupalcode.org/project/drupal/-/blob/11.x/core/phpunit.xml.dist?ref_type=heads#L84

## [4.0.0-alpha3]

### Changed

- Update the phpunit config file to make them work with the changes done to
`core/tests/bootstrap.php` in Drupal 11.1.

## [4.0.0-alpha2]

### Updated

- Update enlightn/security-checker to 2.0

## [4.0.0-alpha1]

### Updated

- Remove sebastian/phpcpd and replace it with systemsdk/phpcpd.

### Removed

- Remove D10 support.
- Remove D10.3 from travis.

## [3.0.2]

### Added

- Add PHPunit 10 support.

## [3.0.1]

### Added

- Add Drupal 10 support because this is necessary for upgrading.

## [3.0.0]

### Added

- Add Drupal 11 support

### Removed

- Remove Drupal 10 support
- Remove support for php 8.1
- Remove nielsdeblaauw/twigcs-a11y module

## [2.x releases]

Older releases (2.x.x) see [CHANGELOG-2.x.md](CHANGELOG-1.x.md).

## [1.x releases]

Older releases (1.x.x) see [CHANGELOG-1.x.md](CHANGELOG-1.x.md).

[5.0.0]: https://github.com/district09/php_package_qa-drupal/compare/4.1.4...5.0.0
[4.1.4]: https://github.com/district09/php_package_qa-drupal/compare/4.1.3...4.1.4
[4.1.3]: https://github.com/district09/php_package_qa-drupal/compare/4.1.2...4.1.3
[4.1.2]: https://github.com/district09/php_package_qa-drupal/compare/4.1.1...4.1.2
[4.1.1]: https://github.com/district09/php_package_qa-drupal/compare/4.1.0...4.1.1
[4.1.0]: https://github.com/district09/php_package_qa-drupal/compare/4.0.0...4.1.0
[4.0.0]: https://github.com/district09/php_package_qa-drupal/compare/4.0.0-alpha6...4.0.0
[4.0.0-alpha6]: https://github.com/district09/php_package_qa-drupal/compare/4.0.0-alpha5...4.0.0-alpha6
[4.0.0-alpha5]: https://github.com/district09/php_package_qa-drupal/compare/4.0.0-alpha4...4.0.0-alpha5
[4.0.0-alpha4]: https://github.com/district09/php_package_qa-drupal/compare/4.0.0-alpha3...4.0.0-alpha4
[4.0.0-alpha3]: https://github.com/district09/php_package_qa-drupal/compare/4.0.0-alpha2...4.0.0-alpha3
[4.0.0-alpha2]: https://github.com/district09/php_package_qa-drupal/compare/4.0.0-alpha1...4.0.0-alpha2
[4.0.0-alpha1]: https://github.com/district09/php_package_qa-drupal/compare/3.0.2...4.0.0-alpha1
[3.0.2]: https://github.com/district09/php_package_qa-drupal/compare/3.0.1...3.0.2
[3.0.1]: https://github.com/district09/php_package_qa-drupal/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/district09/php_package_qa-drupal/compare/2.1.0...3.0.0

[2.x releases]: CHANGELOG-2.x.md
[1.x releases]: CHANGELOG-1.x.md
