# Isaac Mod Manager (IMM)

[![Build Status](http://img.shields.io/travis/Anahkiasen/isaac-mod-manager.svg?style=flat-square)](https://travis-ci.org/Anahkiasen/isaac-mod-manager)
[![Latest Stable Version](http://img.shields.io/packagist/v/anahkiasen/isaac-mod-manager.svg?style=flat-square)](https://packagist.org/packages/anahkiasen/isaac-mod-manager)
[![Total Downloads](http://img.shields.io/packagist/dt/anahkiasen/isaac-mod-manager.svg?style=flat-square)](https://packagist.org/packages/anahkiasen/isaac-mod-manager)
[![Scrutinizer Quality Score](http://img.shields.io/scrutinizer/g/anahkiasen/isaac-mod-manager.svg?style=flat-square)](https://scrutinizer-ci.com/g/anahkiasen/isaac-mod-manager/)
[![Code Coverage](http://img.shields.io/scrutinizer/coverage/g/anahkiasen/isaac-mod-manager.svg?style=flat-square)](https://scrutinizer-ci.com/g/anahkiasen/isaac-mod-manager/)

![](http://i.imgur.com/994Z9a1.png)

IMM is a cross-plateform CLI-tool to manage your Workshop mods for Binding of Isaac.

What it does is check which mods of yours are graphical only (non-LUA) and installs them in `resources/`, allowing you to play without achievements being disabled. While it can install LUA mods they would all overwrite each other since their entry point `main.lua` is always the same, hence why it does the distinction.

This allows you to always have your mods up to date and with your latest subscriptions without having to copy and overwrite files by hand every time.

**This does not affect your Workshop mods in any way**

## Installation

### Prerequisites


IMM requires a terminal and PHP 7+ in order to work. To check if you have PHP, open your terminal of choice (Powershell per example on Windows or Terminal elsewhere) and run `php --version`. If nothing is returned or a version inferior to 7 is returned you'll need to install it, otherwise you're good. To install PHP:

- If you're on Mac you're good
- On Linux use your package manager of choice (aptitude, yum, etc.).
- On Windows you can install it through [Chocolatey](https://chocolatey.org/install) via `choco install php`.

### Through Composer

#### Stable version

IMM can be installed through [Composer](https://getcomposer.org/) (this requires the OpenSSL extension[1]):

```bash
$ composer global require anahkiasen/isaac-mod-manager
```

#### Development version

You can also install the beta version like this:

```bash
$ composer global require "anahkiasen/isaac-mod-manager:1.0.x@dev"
```

### Via the PHAR archive

Alternatively you can use the PHAR archive, which you can download in the [Releases](https://github.com/Anahkiasen/isaac-mod-manager/releases) page.

Once downloaded you use it as such (per example):

```bash
$ php imm.phar mods:install
```

## Usage

You can run `imm` to see a list of possible commands and their description.

- To install your mods simply run `imm mods:install`, and to uninstall them but keep Isaac modded run `imm mods:uninstall`.
- You can install or uninstall one or more specific mods by specifiying their Steam ID: `imm mods:install 123456789 123456789`.
- You can also find mods by using a part of their name (case insensitive) `imm mods:install chud mei 123456789`
- Finally you can restore your copy of Isaac to its non-modded state by running `imm restore`.

## Building

To compress the app into a `imm.phar` archive run:

```bash
$ composer build
```

## Testing

To run the test suite, run the following:

```bash
$ composer test
```

## Roadmap

You can find the currently planned features in the [Milestones](https://github.com/Anahkiasen/isaac-mod-manager/milestones) page.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email ehtnam6@gmail.com instead of using the issue tracker.

## Credits

- [Anahkiasen](https://github.com/Anahkiasen)
- [All Contributors](https://github.com/anahkiasen/isaac-mod-manager/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[1]: You can install it by uncommenting the first line containing `openssl` in `C:/tools/php71/php.ini`
