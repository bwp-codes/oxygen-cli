# WP CLI Commands for Oxygen Builder

## Usage via Composer

1. run `composer require bwp-codes/oxygen-cli`.

## Install as regular / mu-plugin

1. Download the current [Release](https://github.com/bwp-codes/oxygen-cli/releases)
2. Regular Plugin: Place the zip into `/wp-content/plugins/`
3. Must-Use Plugin: Place the zip into `/wp-content/mu-plugins/`
4. Extract zip into folder and rename folder to oxygen-cli
5. Activate Plugin in WP Backend

## Tested Oxygen versions
- 4
- 3.9
- 3.8

## Available Commands

-   `wp oxygen sign-shortcode <post_type>` to sign shortcodes on specific post types.
-   `wp oxygen css-cache` to regenerate the CSS cache.

### Credit

WP CLI commands based on the original work of https://github.com/artifact-modules/command (MIT license)

Huge thanks to [@mickaelperrin](https://github.com/mickaelperrin) for fixing crucial bugs and actually making things work! <3
