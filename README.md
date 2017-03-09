# Isaac Mod Manager (IMM)

![](http://i.imgur.com/994Z9a1.png)

IMM is a cross-plateform CLI-tool to manage your Workshop mods for Binding of Isaac.

What it does is check which mods of yours are graphical only (non-LUA) and installs them in `resources/`, allowing you to play without achievements being disabled. While it can install LUA mods they would all overwrite each other since their entry point `main.lua` is always the same, hence why it does the distinction.

This allows you to always have your mods up to date and with your latest subscriptions without having to copy and overwrite files by hand every time.

**This does not affect your Workshop mods in any way**

## Installation

### Stable version

IMM can be installed through [Composer](https://getcomposer.org/):

```bash
$ composer global require anahkiasen/isaac-mod-manager
```

Or by downloading one of the [prepackaged archives](https://github.com/Anahkiasen/isaac-mod-manager/releases).

### Development version

You can also install the beta version like this:

```bash
$ composer global require "anahkiasen/isaac-mod-manager:1.0.x@dev"
```

## Usage

You can run `imm` to see a list of possible commands and their description. 

- To install your mods simply run `imm mods:install`, and to uninstall them but keep Isaac modded run `imm mods:uninstall`.
- You can install or uninstall one or more specific mods by specifiying their Steam ID: `imm mods:install 123456789 123456789`.
- Finally you can restore your copy of Isaac to its non-modded state by running `imm restore`.

## Building

``` bash
$ composer build
```

## Roadmap

- Find a way to make the app extract the resources itself instead of asking the user to do it.
- Allow installing/uninstalling mods by matching the name in addition to by SteamID (ie `mods:install chud` would find the first mod with purple in its name)
- Allow you to pick one LUA mod you want to be installed
- Smarter install/uninstall (ie don't say we uninstalled something if it was already uninstalled and vice versa)
- Add some tests

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email ehtnam6@gmail.com instead of using the issue tracker.

## Credits

- [Anahkiasen](https://github.com/Anahkiasen)
- [All Contributors](https://github.com/anahkiasen/isaac-mod-manager/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
