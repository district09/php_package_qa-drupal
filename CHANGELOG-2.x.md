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

[2.1.1]: https://github.com/district09/php_package_qa-drupal/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/district09/php_package_qa-drupal/compare/2.0.3...2.1.0
[2.0.3]: https://github.com/district09/php_package_qa-drupal/compare/2.0.2...2.0.3
[2.0.2]: https://github.com/district09/php_package_qa-drupal/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/district09/php_package_qa-drupal/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/district09/php_package_qa-drupal/compare/1.8.1...2.0.0
