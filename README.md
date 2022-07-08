# WP CLI Commands for Oxygen Builder

## Usage via Composer

1. Add this repo to your composer.json under `repositories`:
```
"repositories": [
    {
      "type": "git",
      "url": "git@github.com:bwp-codes/oxygen-cli.git",
    },
],
...
```

2. run `composer require bwp-codes/oxygen-cli`.

## Install as regular / mu-plugin

1. Download the current [Release](https://github.com/bwp-codes/oxygen-cli/releases)
2. Regular Plugin: Place the zip into `/wp-content/plugins/`
3. Must-Use Plugin: Place the zip into `/wp-content/mu-plugins/`
4. Extract zip into folder and rename folder to oxygen-cli
5. Activate Plugin in WP Backend

## Tested Oxygen versions
- 4
- 3.9

## Available Commands

-   `wp oxygen sign-shortcodes` to sign shortcodes
-   `wp oxygen css-cache` to regenerate the CSS cache

### Credit

WP CLI commands based on the work of https://github.com/artifact-modules/command (MIT license)
