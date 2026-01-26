# wp-teamized

This is a WordPress Plugin for integrating with [django-teamized](https://github.com/rafaelurben/django-teamized).

## Features

- **Settings page**: "Teamized" (under Settings group)
- **Blocks:**
    - **Club Member Portfolios**: Display club group member profiles in a responsive grid

## Installation

1. Install the [git updater](https://github.com/afragen/git-updater) plugin.
   1. Download the latest archive from [Releases](https://github.com/afragen/git-updater/releases)
   2. Go to the "Plugins → Add Plugin" screen and click the "Upload Plugin" button.
   3. Upload the zipped archive directly.
   4. Click "Activate Plugin".
   5. Go to the "Settings → Git Updater" screen and click "Activate Free Version".
2. Install the "wp-teamized" plugin:
   1. Download the latest zip archive from [Releases](https://github.com/rafaelurben/wp-teamized/releases).
   2. Go to the "Plugins → Add Plugin" screen and click the "Upload Plugin" button.
   3. Upload the zipped archive directly.
   4. Click "Activate Plugin".

> [!WARNING]
> If the plugin is installed via Git Updater settings, translations may not work correctly.
> Please install the plugin via zip upload as described above.

## Development Setup

1. Clone this repository to your local machine.
2. Create a local WordPress environment (e.g., using Local by Flywheel, XAMPP, or MAMP).
3. Create a symbolic link from the plugin directory in your WordPress installation to the cloned repository. For
   example, on Windows, you can use the following command in Command Prompt (run as Administrator):
   ```shell
   mklink /D "C:\path-to-wordpress\wp-content\plugins\wp-teamized" "C:\path-to-repo"
   ```
4. Activate the plugin from the WordPress admin dashboard.

## Translation

This plugin uses WordPress's built-in translation system with text domain `wp-teamized`.
The compiled translation files (`.mo` and `.json`) are not included in the repository.
They are generated from the `.po` files in the release pipeline.

Use WP-CLI to generate `.pot` and `.po` files:

```bash
# Generate .pot template
wp i18n make-pot . languages/wp-teamized.pot
# Update .po files for each language
wp i18n update-po languages/wp-teamized.pot

# Now update the .po files with your translations...

# Compile .po files to .mo
wp i18n make-mo languages/
# Generate .json files for JavaScript translations from .po files
wp i18n make-json languages/
```

You may also use the Docker version (PowerShell):

```shell
# Generate .pot template
docker run --rm --volume "${PWD}:/var/www/html/wp-content/plugins/wp-teamized" --workdir /var/www/html/wp-content/plugins/wp-teamized wordpress:cli wp i18n make-pot . languages/wp-teamized.pot
# Update .po files for each language
docker run --rm --volume "${PWD}:/var/www/html/wp-content/plugins/wp-teamized" --workdir /var/www/html/wp-content/plugins/wp-teamized wordpress:cli wp i18n update-po languages/wp-teamized.pot

# Now update the .po files with your translations...

# Compile .po files to .mo
docker run --rm --volume "${PWD}:/var/www/html/wp-content/plugins/wp-teamized" --workdir /var/www/html/wp-content/plugins/wp-teamized wordpress:cli wp i18n make-mo languages/
# Generate .json files for JavaScript translations from .po files
docker run --rm --volume "${PWD}:/var/www/html/wp-content/plugins/wp-teamized" --workdir /var/www/html/wp-content/plugins/wp-teamized wordpress:cli wp i18n make-json languages/
```
