# Changelog

All Notable changes to the **Quality Assurance - Drupal** package.

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

[1.4.1]: https://github.com/digipolisgent/php_package_qa-drupal/compare/1.4.0...1.4.1
[1.4.0]: https://github.com/digipolisgent/php_package_qa-drupal/compare/1.3.1...1.4.0
[1.3.1]: https://github.com/digipolisgent/php_package_qa-drupal/compare/1.3.0...1.3.1
[1.3.0]: https://github.com/digipolisgent/php_package_qa-drupal/compare/1.2.3...1.3.0
[1.2.3]: https://github.com/digipolisgent/php_package_qa-drupal/compare/1.2.2...1.2.3
[1.2.2]: https://github.com/digipolisgent/php_package_qa-drupal/compare/1.2.1...1.2.2
[1.2.1]: https://github.com/digipolisgent/php_package_qa-drupal/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/digipolisgent/php_package_qa-drupal/compare/1.1.5...1.2.0
[1.1.5]: https://github.com/digipolisgent/php_package_qa-drupal/compare/1.1.4...1.1.5
[1.1.4]: https://github.com/digipolisgent/php_package_qa-drupal/compare/1.1.3...1.1.4
[1.1.3]: https://github.com/digipolisgent/php_package_qa-drupal/compare/1.1.2...1.1.3
[1.1.2]: https://github.com/digipolisgent/php_package_qa-drupal/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/digipolisgent/php_package_qa-drupal/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/digipolisgent/php_package_qa-drupal/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/digipolisgent/php_package_qa-drupal/releases/tag/1.0.0
