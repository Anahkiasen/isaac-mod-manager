# CHANGELOG

## 0.4.0 - Unreleased
### Added
- Added ability to clear conflicts resolution cache only with `imm cache:clear --conflicts`

## 0.3.0 - 2017-03-25
### Added
- Added `--select` flag to the `mods:*` commands which allows you to pick precisely which mods to install/uninstall
- Added ability to install/uninstall mods by text in addition to Steam ID (eg. `chud` will match `Chud Lite+`)
- Added automatic resources extraction on Unix platforms
- Added autoupdates to the PHAR via the `imm.phar self-update` command
- Added better logic to guess the paths to the game and mods
- Added conflicts resolver system
- Added progress bar during initial `resources` backup
- Added warning when installing mods that were in booster packs, and offer to filter them out

### Changed
- The `mods:install` can now install LUA mod by passing the `--lua` flag, **this is very experimental**

### Fixed
- Fixed binary always showing "dev version" even on stable builds
- Fixed issue with some mods being ignored due to invalid characters in their `metadata.xml` file
- Fixed Mac support

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