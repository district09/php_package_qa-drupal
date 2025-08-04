# Changelog

All Notable changes to the **Quality Assurance - Drupal** package.

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

## [2.1.1]

### Added

- Add custom theme(s) node_modules to the phpcpd exclude patterns.

## [2.1.0]

### Changes

- Update the phpunit config file to make them work with the changes done to
`core/tests/bootstrap.php` in Drupal 10.2.
- Update the minimal Drupal version to 10.2.
- Drop support for Drupal 9.

## [2.0.3]

### Changed

- Update the phpunit config files to comply to the new xml schema since phpunit
  9.3.0
- Update minimal phpunit version to 9.3.0.

## [2.0.2]

### Changed

- Change to browserkit instead of goutte.
  Use the browserkit_http driver instead og the goutte driver. See:
  https://github.com/jhedstrom/drupalextension/commit/2ab66a7eae53ae4e1e5824edaa72e98039b084db

## [2.0.1]

### Fixed

- Fix Drupal support higher than 10.0.0.

## [2.0.0]

### Added

- Add Drupal 10 support.

### Changed

- Change minimal PHP version to 8.1.

### Removed

- Remove Drupal 9.3.x support.

## [1.8.1]

### added

- Add exclude node modules from PHPUnit.

## [1.8.0]

### Added

- Add excluding hand-crafted mocks from coverage reports. This will exclude
  file names ending with Stub.php or Spy.php.

## [1.7.3]

### Added

- Add missing licence information.

## [1.7.2]

### Added

- Add travis build.

### Changed

- Change minimal Drupal version to 9.3.

## [1.7.1]

### Added

- Add support for php 8.0
- Add support for PHP 8.1

## [1.7.0]

### Added

- Add support for main branches.
- Add support for release/x.x.x branches.
- Add support for hotfix/x.x.x branches.
- Add support for RELEASE commit messages.
- Add support for Merge commit messages.

## [1.6.2]

### Added

- Add php compatibility checker.

## [1.6.1]

### Added

- Add exclusion rule for JS/CSS.

## [1.6.0]

### Added

- Add support for PHP 8.1.

## [1.5.6]

### Fixed

- Fix not allowed & in XML description.

## [1.5.5]

### Fixed

- Fix not closed XML tags in phpcs*.xml.

## [1.5.4]

### Fixed

- Fix broken local PHP CodeSniffer run.

## [1.5.3]

### Fixed

- Fix failing CodeClimate builds by changing `config/phpcs.xml` config file.

## [1.5.2]

### Changed

- Add the *.install extension to the excluded files for PHP Mess Detector.

## [1.5.1]

### Changed

- Add the *.install extension to the excluded files for PHP CodeSniffer.

## [1.5.0]

### Changed

- Change minimal PHP version to 7.4.
- Change minimal Drupal version to 9.1.
- Change phpmd from shim to "normal" package.
- Change phpcpd from shim to "normal" package.

### Updated

- Update phpstan to 1.x.x.

## [1.4.13]

### Added

- Add twig files of custom modules to the twigcs task.

## [1.4.12]

### Changed

- Update minimal requirement for grumphp.

## [1.4.11]

### Fixed

- Fix deprecated securitychecker from SensioLabs
- Fix twigcs issue with invalid path

## [1.4.10]

### Added

- Add Official Twig linter, see documentation on
  https://github.com/phpro/grumphp/blob/master/doc/tasks/twigcs.md
- Add WCAG Twig linter, see https://github.com/NielsdeBlaauw/twigcs-a11y

## [1.4.9]

### Fixed

- Fix spaces in phpstan.neon config file.

## [1.4.8]

### Fixed

- Fix grumphp config for Drupal websites.
- Fix phpstan config for Drupal websites.

## [1.4.7]

### Fixed

- Fix deprecation detection levels.
  The Symfony deprecations helper is now disabled. The detection of deprecations
  is done through PHPStan.

## [1.4.6]

### Fixed

- Fix allowed number of deprecation errors.

## [1.4.5]

### Fixed

- Fix missing use statements.

## [1.4.4]

### Added

- Add setting up a fake Drupal root for tasks who requires it.
- Add composer_normalize to the GrumPHP tasks.

### Fixed

- Fix the PHPUnit/Symfony deprecation warning (Drupal 8).
- Fix incompatible UnitTest::setUp() method (Drupal 9).

## [1.4.3]

### Added

- Add sensiolabs/security-checker.
- Add support for Drupal 9.1 by adding "^9" to the required PHPUnit versions.

### Removed

- Remove older PHPUnit version bridge.

## [1.4.2]

### Added

- Add support for Drupal 9.1 and 9.2.

## [1.4.1]

### Added

- Add support for Drupal 9.x.
- Add exception to allow short `$id` variable and `id()` method names.

## [1.4.0]

This release has a major change in the default quality rules. The default
standards are strict. Legacy modules and websites where it takes to much time
to comply to these standards can overwrite them by adding custom configuration
files to their project root. See the README of this project.

### Added

- Add vendor to the excludes for extension.
- Add extra information how to configure PHPStorm in README.

### Changed

- Change the code standards to strict by default.
- Change how Unit tests are run within a module repository: they now run without
  the need of a drupal project outside the repository root.
- Change PHP CodeSniffer rules so they follow drupal.org.
- Change PHP Mess Detector rules so they have proper defaults and support drupal
  codebase.
- Change minimal PHP version to 7.3.

### Fixed

- Fix timeout issues when running GrumPHP tasks on large code base.
- Fix detecting the task info for config files.
- Fix missing backslash before dot in the allowed branch patterns.
- Fix PhpStan Reflection errors during git commit of Drupal module.

### Removed

- Remove config parameters that are same as default.
- Remove unneeded PhpStan level in the grumphp config file, this is now
  overridable trough the PhpStan neon configuration file.

## [1.3.1]

### Removed

- Remove version from install command README.
- Remove Drupal 9 support.
- Remove dotenv requirement.

## [1.3.0]

### Changed

- Use the shim version of various tools to avoid dependency conflicts.

### Updated

- Update to GrumPHP 1.1.

## [1.2.3]

### Added

- Add support for digits in the project key.

## [1.2.2]

### Added

- Add PHPStan ignore rule for undefined typed data properties.

## [1.2.1]

### Fixed

- Fix namespace Behat contexts.
- Ignore test directories with PHPStan.

## [1.2.0]

### Fixed

- Fix various issues for extension projects.

## [1.1.5]

### Added

- Add missing phpunit-bridge package.

## [1.1.4]

### Fixed

- Fix missing namespace separator in the PhpUnit config file for extensions.

## [1.1.3]

### Fixed

- Fix PHPStan configuration.

## [1.1.2]

### Added

- Add D9 deprecations configuration.

### Removed

- Remove checkMissingIterableValueType from PHPStan config.

## [1.1.1]

### Updated

- Update code formatting.

### Changed

- Change regex type git blacklist.

## [1.1.0]

### Added

- Add support for overriding config files.

### Updated

- Update PHPStan config.
- Update code formatting.

### Changed

- Change package vendor.
- Change package name to digipolisgent/qa-drupal.

## [1.0.0]

### Added

Initial setup of the qa-drupal package:

- Default config files and checks for a Drupal site.
- Default config files and checks for a Drupal module.

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
[2.1.1]: https://github.com/district09/php_package_qa-drupal/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/district09/php_package_qa-drupal/compare/2.0.3...2.1.0
[2.0.3]: https://github.com/district09/php_package_qa-drupal/compare/2.0.2...2.0.3
[2.0.2]: https://github.com/district09/php_package_qa-drupal/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/district09/php_package_qa-drupal/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/district09/php_package_qa-drupal/compare/1.8.1...2.0.0
[1.8.1]: https://github.com/district09/php_package_qa-drupal/compare/1.8.0...1.8.1
[1.8.0]: https://github.com/district09/php_package_qa-drupal/compare/1.7.3...1.8.0
[1.7.3]: https://github.com/district09/php_package_qa-drupal/compare/1.7.2...1.7.3
[1.7.2]: https://github.com/district09/php_package_qa-drupal/compare/1.7.1...1.7.2
[1.7.1]: https://github.com/district09/php_package_qa-drupal/compare/1.7.0...1.7.1
[1.7.0]: https://github.com/district09/php_package_qa-drupal/compare/1.6.2...1.7.0
[1.6.2]: https://github.com/district09/php_package_qa-drupal/compare/1.6.1...1.6.2
[1.6.1]: https://github.com/district09/php_package_qa-drupal/compare/1.6.0...1.6.1
[1.6.0]: https://github.com/district09/php_package_qa-drupal/compare/1.5.6...1.6.0
[1.5.6]: https://github.com/district09/php_package_qa-drupal/compare/1.5.5...1.5.6
[1.5.5]: https://github.com/district09/php_package_qa-drupal/compare/1.5.4...1.5.5
[1.5.4]: https://github.com/district09/php_package_qa-drupal/compare/1.5.3...1.5.4
[1.5.3]: https://github.com/district09/php_package_qa-drupal/compare/1.5.2...1.5.3
[1.5.2]: https://github.com/district09/php_package_qa-drupal/compare/1.5.1...1.5.2
[1.5.1]: https://github.com/district09/php_package_qa-drupal/compare/1.5.0...1.5.1
[1.5.0]: https://github.com/district09/php_package_qa-drupal/compare/1.4.13...1.5.0
[1.4.13]: https://github.com/district09/php_package_qa-drupal/compare/1.4.12...1.4.13
[1.4.12]: https://github.com/district09/php_package_qa-drupal/compare/1.4.11...1.4.12
[1.4.11]: https://github.com/district09/php_package_qa-drupal/compare/1.4.10...1.4.11
[1.4.10]: https://github.com/district09/php_package_qa-drupal/compare/1.4.9...1.4.10
[1.4.9]: https://github.com/district09/php_package_qa-drupal/compare/1.4.8...1.4.9
[1.4.8]: https://github.com/district09/php_package_qa-drupal/compare/1.4.7...1.4.8
[1.4.7]: https://github.com/district09/php_package_qa-drupal/compare/1.4.6...1.4.7
[1.4.6]: https://github.com/district09/php_package_qa-drupal/compare/1.4.5...1.4.6
[1.4.5]: https://github.com/district09/php_package_qa-drupal/compare/1.4.4...1.4.5
[1.4.4]: https://github.com/district09/php_package_qa-drupal/compare/1.4.3...1.4.4
[1.4.3]: https://github.com/district09/php_package_qa-drupal/compare/1.4.2...1.4.3
[1.4.2]: https://github.com/district09/php_package_qa-drupal/compare/1.4.1...1.4.2
[1.4.1]: https://github.com/district09/php_package_qa-drupal/compare/1.4.0...1.4.1
[1.4.0]: https://github.com/district09/php_package_qa-drupal/compare/1.3.1...1.4.0
[1.3.1]: https://github.com/district09/php_package_qa-drupal/compare/1.3.0...1.3.1
[1.3.0]: https://github.com/district09/php_package_qa-drupal/compare/1.2.3...1.3.0
[1.2.3]: https://github.com/district09/php_package_qa-drupal/compare/1.2.2...1.2.3
[1.2.2]: https://github.com/district09/php_package_qa-drupal/compare/1.2.1...1.2.2
[1.2.1]: https://github.com/district09/php_package_qa-drupal/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/district09/php_package_qa-drupal/compare/1.1.5...1.2.0
[1.1.5]: https://github.com/district09/php_package_qa-drupal/compare/1.1.4...1.1.5
[1.1.4]: https://github.com/district09/php_package_qa-drupal/compare/1.1.3...1.1.4
[1.1.3]: https://github.com/district09/php_package_qa-drupal/compare/1.1.2...1.1.3
[1.1.2]: https://github.com/district09/php_package_qa-drupal/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/district09/php_package_qa-drupal/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/district09/php_package_qa-drupal/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/district09/php_package_qa-drupal/releases/tag/1.0.0
