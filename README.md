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
2. Go to "Settings → Git Updater → Install Plugin".
3. In the "Plugin URI" field, enter: `https://github.com/rafaelurben/wp-teamized`
4. In the "Repository Branch" field, enter: `main`
5. Click the "Install Plugin" button.
6. After installation, activate the plugin.

## Development Setup

1. Clone this repository to your local machine.
2. Create a local WordPress environment (e.g., using Local by Flywheel, XAMPP, or MAMP).
3. Create a symbolic link from the plugin directory in your WordPress installation to the cloned repository. For
   example, on Windows, you can use the following command in Command Prompt (run as Administrator):
   ```shell
   mklink /D "C:\path-to-wordpress\wp-content\plugins\wp-teamized" "C:\path-to-repo"
   ```
4. Activate the plugin from the WordPress admin dashboard.
