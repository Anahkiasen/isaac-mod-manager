# Isaac Mod Manager (IMM)

IMM is a cross-plateform CLI-tool to manage your graphical Workshop mods for Binding of Isaac.

What it does is check which mods of yours are graphical only (non-LUA) and installs them in `resources/`, allowing you to play without achievements being disabled.
This allows you to always have your mods up to date and with your latest subscriptions without having to copy and overwrite files by hand every time.

**This does not affect your Workshop mods in any way**

## Installation

IMM can be installed through [Composer](https://getcomposer.org/):

```bash
$ composer global require anahkiasen/isaac-mod-manager
```

Or by downloading one of the [prepackaged archives](https://github.com/Anahkiasen/isaac-mod-manager/releases).

## Usage

You can run `imm` to see a list of possible commands and their description. 

To install your mods simply run `imm install`, and to uninstall them but keep Isaac modded run `imm uninstall`.

## Building

``` bash
$ composer build
```

## Roadmap

- Find a way to make the app extract the resources itself instead of asking the user to do it.
- Ability to install/uninstall a mod in particular
- Add some tests

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email :author_email instead of using the issue tracker.

## Credits

- [Anahkiasen](https://github.com/Anahkiasen)
- [All Contributors](https://github.com/rocketeers/rocketeer/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.