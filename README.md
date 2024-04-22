# Embed Peertube Playlists and Livestreams

Embed Peertube playlists and livestreams on wordpress.

## Installation

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.

## How to use the plugin

3. Update the Peertube instance. Click "Settings".

   ![Settings](docs/assets/settings.png)

4. Update the Instance.

   ![Update Instance](docs/assets/instance.png)

5. Save the settings.

   ![Save Instance Settings](docs/assets/save.png)

6. Go to the 'channels' or 'playlists' submenu.

   ![Playlists](docs/assets/playlists.png)

7. Create a shortcode by saving the ID number from the playlist and create an element.

   ![Share](docs/assets/share.png)

8. You can find the ID in the playlist, then share it. In the example, it is this number: c5Rx7fX02wf4ssEaRJMJYb.

   ![Share](docs/assets/shareID.png)

9. Add the Playlist or Livestream ID and add it.

   ![addPlaylistId](docs/assets/addPlaylistId.png)

10. Copy the shortcode.

    ![copyShortcode](docs/assets/copyShortcode.png)

11. Go to the page where you wish to add the 'channel' or 'playlist'.
12. Add the shortcode.

## Deployment

Before deploying it, please ensure which version of the plugin is installed and change the API endpoints.

Zip it and upload it to WP.

- zip -r v1.8.0embed-peertube-wp.zip ./embed-peertube-wp

### PS: it only works with public channels and playlists.

**Contributors:** fairkom / biowilli  
**Donate link:** [https://www.fairkom.eu/](https://www.fairkom.eu/)  
**Tags:** channel, playlist, peertube, video, grid, slider, list, peertube playlist, playlist peertube, peertube plugin, video playlist, peertube player, peertube integration, peertube api, player video, video integration, peertube embed, channels embed  
**Requires at least:** 3.5  
**Tested up to:** 6.1  
**Requires PHP:** 5.6  
**Stable tag:** 2.0.0  
**License:** GPLv3 or later  
**License URI:** [http://www.gnu.org/licenses/gpl-2.0.html](http://www.gnu.org/licenses/gpl-2.0.html)
