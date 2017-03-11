# CHANGELOG

## 0.3.0 - Unreleased
### Added
- Added conflcits resolver system
- Added better logic to guess the paths to the game and mods
- Added ability to install/uninstall mods by text in addition to Steam ID (eg. `chud` will match `Chud Lite+`)
- Added automatic resources extraction on Unix platforms

### Changed
- The `mods:install` command now tries to install all mods, pass `-g` to only install non-LUA mods
- Better Mac support

### Fixed
- Fixed binary always showing "dev version" even on stable builds

## 0.2.3 - 2017-03-09
### Fixed
- Fixed crash during mod uninstallation

## 0.2.2 - 2017-03-09
### Changed
- Removed dependency on `mbstring` extension

## 0.2.1 - 2017-03-09
### Fixed
- Fixed some PHAR compatibility issues

## 0.2.0 - 2017-03-09
### Added
- Added `restore` command
- Added better error handling for various situations

### Changed
- Mods-related commands are now under the `mods:` prefix

### Fixed
- Reduced size of archive
- Fix Bash on Windows compatibility

## 0.1.0 - 2017-03-08
### Added
- Initial release