# WP CLI Commands for Oxygen Builder

:warning: :construction: Currently broken under Oxygen 3.9 :construction: :warning:

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

## Available Commands

-   `wp oxygen sign-shortcodes` to sign shortcodes
-   `wp oxygen css-cache` to regenerate the CSS cache

### Credit

WP CLI commands based on the work of https://github.com/artifact-modules/command (MIT license)
