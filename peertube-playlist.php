<?php

/*
Plugin Name: Embed-Peertube
Plugin URI: 
Version: 2.00
Description: Display Peertube Playlists and Channels
Author: Monz Philipp
Author URI: https://www.fairkom.com/en/shop
Network: false
Text Domain: embed-peertube
Domain Path: 
*/

// Register activation and deactivation hooks for the plugin
register_activation_hook(__FILE__, "add_peertube_install");
register_uninstall_hook(__FILE__, "remove_peertube_desinstall");

function add_peertube_install()
{
    global $wpdb;

    // Define table names
    $channels_peertube_table = $wpdb->prefix . "channels_peertube";
    $playlist_peertube_table = $wpdb->prefix . "playlists_peertube";

    // Include WordPress upgrade functions
    require_once ABSPATH . "wp-admin/includes/upgrade.php";

    // Create SQL query for channel table
    $channel_sql =
        "
		CREATE TABLE `" .
        $channels_peertube_table .
        "` (
			id int(11) NOT NULL AUTO_INCREMENT,
			channel_id varchar(50) NOT NULL,
			PRIMARY KEY  (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
	";

    // Create SQL query for playlist table
    $playlist_sql =
        "
		CREATE TABLE `" .
        $playlist_peertube_table .
        "` (
			id int(11) NOT NULL AUTO_INCREMENT,
			name varchar(50) NOT NULL,
			playlist_id varchar(50) NOT NULL,
			show_title int(1) NOT NULL,
			show_description int(1) NOT NULL,
			template int(11) NOT NULL,
			text_size int(3) NOT NULL,
			text_color varchar(20) NOT NULL,
			desc_text_color varchar(20) NOT NULL,
			bg_color varchar(20) NOT NULL,
			PRIMARY KEY  (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
	";

    // Execute the queries
    dbDelta($channel_sql);
    dbDelta($playlist_sql);
}

function remove_peertube_desinstall()
{
    global $wpdb;

    // Define table names
    $playlist_peertube_table = $wpdb->prefix . "playlists_peertube";
    $channels_peertube_table = $wpdb->prefix . "channels_peertube";

    // Create SQL queries to drop the tables
    $playlist_sql = "DROP TABLE " . $playlist_peertube_table;
    $channels_sql = "DROP TABLE " . $channels_peertube_table;

    // Execute the queries
    $wpdb->query($playlist_sql);
    $wpdb->query($channels_sql);
}

// Add a new menu item to the WordPress admin menu
add_action("admin_menu", "register_peertube_plugin_menu");
function register_peertube_plugin_menu()
{
    // Add a top-level menu item for the plugin
    add_menu_page(
        "Information",
        "Embed Peertube",
        "edit_pages",
        "playlists_peertube",
        "information_peertube",
        "dashicons-video-alt2",
        32
    );

    // Add a sub-menu item for the plugin settings
    add_submenu_page(
        "playlists_peertube",
        "Manage Channels",
        "Channel",
        "manage_options",
        "channel-pt",
        "channels_peertube"
    );
    add_submenu_page(
        "playlists_peertube",
        "Manage Playlists",
        "Playlists",
        "manage_options",
        "playlist-pt",
        "playlists_peertube"
    );
    add_submenu_page(
        "playlists_peertube",
        "Settings for Peertube Playlist and channels",
        "Settings",
        "manage_options",
        "playlist-pt-settings",
        "display_peertube_settings"
    );
    // Call function to register settings for the plugin
    add_action("admin_init", "register_playlist_peertube_settings");
}

// Add stylesheet to the WordPress admin area
add_action("admin_print_styles", "playlist_peertube_css");
function playlist_peertube_css()
{
    wp_enqueue_style(
        "PlaylistPTStylesheet",
        plugins_url("css/admin.css", __FILE__)
    );
}

// Register settings for the plugin
function register_playlist_peertube_settings()
{
    add_option("pl_peertube_url", "https://fair.tube");
    add_option('pl_grid_backgroundcolor', '#575757');
    add_option('pl_grid_textcolor', '#FFFFFF');

    add_option('pl_grid_textsize_header', '20');
    add_option('pl_grid_textsize_description', '14');

    add_option('pl_playbutton_style_grid');
    add_option('pl_playbutton_style_playlist');
    add_option('playbutton_black_grid');
    add_option('playbutton_white_grid');
    add_option('playbutton_fs1_grid');
    add_option('playbutton_fs1_2_grid');

    add_option('playbutton_black_playlist');
    add_option('playbutton_white_playlist');
    add_option('playbutton_fs1_playlist');
    add_option('playbutton_fs1_2_playlist');

    add_option("pl_grid_margin_top", 10); 
    add_option("pl_grid_margin_bottom", 10); 
    add_option("pl_grid_margin_right", 10); 
    add_option("pl_grid_margin_left", 10); 
    add_option("pl_grid_borderradius", 10);
    add_option('pl_grid_borderradius_top_left');
    add_option('pl_grid_borderradius_top_right');
    add_option('pl_grid_borderradius_bottom_left');
    add_option('pl_grid_borderradius_bottom_right');
    add_option('pl_hover_grid_borderradius_top_left');
    add_option('pl_hover_grid_borderradius_top_right');
    add_option('pl_hover_grid_borderradius_bottom_left');
    add_option('pl_hover_grid_borderradius_bottom_right');
    
    add_option('pl_hover_delay', 300);
}

// Display the settings page for the plugin
function display_peertube_settings()
{
    // Check if the form has been submitted
    if (sizeof($_POST)) {
        // Verify that the form was submitted by an authorized user
        check_admin_referer("pl_peertube_settings");
        $url = sanitize_text_field(rtrim($_POST["pl_peertube_url"], "/"));
        update_option("pl_peertube_url", $url);
        update_option('pl_grid_backgroundcolor', sanitize_text_field($_POST['pl_grid_backgroundcolor']));
        update_option('pl_grid_textcolor', sanitize_text_field($_POST['pl_grid_textcolor']));
        update_option('pl_grid_textsize_header', sanitize_text_field($_POST['pl_grid_textsize_header']));
        update_option('pl_grid_textsize_description', sanitize_text_field($_POST['pl_grid_textsize_description']));

        update_option('playbutton_black_grid', sanitize_text_field($_POST['playbutton_black_grid']));
        update_option('playbutton_white_grid', sanitize_text_field($_POST['playbutton_white_grid']));
        update_option('playbutton_fs1_grid', sanitize_text_field($_POST['playbutton_fs1_grid']));
        update_option('playbutton_fs1_2_grid', sanitize_text_field($_POST['playbutton_fs1_2_grid']));
        update_option('pl_playbutton_style_grid', sanitize_text_field($_POST['pl_playbutton_style_grid']));
        update_option('pl_playbutton_style_playlist', sanitize_text_field($_POST['pl_playbutton_style_playlist']));
        update_option('playbutton_black_playlist', sanitize_text_field($_POST['playbutton_black_playlist']));
        update_option('playbutton_white_playlist', sanitize_text_field($_POST['playbutton_white_playlist']));
        update_option('playbutton_fs1_playlist', sanitize_text_field($_POST['playbutton_fs1_playlist']));
        update_option('playbutton_fs1_2_playlist', sanitize_text_field($_POST['playbutton_fs1_2_playlist']));

        update_option('pl_grid_margin_top', absint($_POST['pl_grid_margin_top']));
        update_option('pl_grid_margin_bottom', absint($_POST['pl_grid_margin_bottom']));
        update_option('pl_grid_margin_right', absint($_POST['pl_grid_margin_right']));
        update_option('pl_grid_margin_left', absint($_POST['pl_grid_margin_left']));
        update_option('pl_grid_borderradius', absint($_POST['pl_grid_borderradius']));
        update_option('pl_grid_borderradius_top_left', absint($_POST['pl_grid_borderradius_top_left']));
        update_option('pl_grid_borderradius_top_right', absint($_POST['pl_grid_borderradius_top_right']));
        update_option('pl_grid_borderradius_bottom_left', absint($_POST['pl_grid_borderradius_bottom_left']));
        update_option('pl_grid_borderradius_bottom_right', absint($_POST['pl_grid_borderradius_bottom_right']));
        update_option('pl_hover_grid_borderradius_top_left', absint($_POST['pl_hover_grid_borderradius_top_left']));
        update_option('pl_hover_grid_borderradius_top_right', absint($_POST['pl_hover_grid_borderradius_top_right']));
        update_option('pl_hover_grid_borderradius_bottom_left', absint($_POST['pl_hover_grid_borderradius_bottom_left']));
        update_option('pl_hover_grid_borderradius_bottom_right', absint($_POST['pl_hover_grid_borderradius_bottom_right']));

        update_option('pl_hover_delay', sanitize_text_field($_POST['pl_hover_delay']));
    } else {
        $type = get_option("nice_page_transition_type");
    }
    // Display the settings form
    include plugin_dir_path(__FILE__) . "views/settings.php";
}

//This function creates a Information for the wordpress Plugin to embed Peertube channel and playlists
function information_peertube()
{
    include plugin_dir_path(__FILE__) . "views/information.php";
}

//This function creates a Peertube channel and allows you to display it using a shortcode in WordPress.
function channels_peertube()
{
    //If the user is an administrator
    if (is_admin()) {
        //Global variable for database access
        global $wpdb;

        //Table to store the channel data
        $channels_peertube_table = $wpdb->prefix . "channels_peertube";
        //TODO check this in more detail::
        //If the form has been submitted
        if (sizeof($_POST) > 0) {
            //If the name or channel ID fields are empty
            if (empty($_POST["channel_id"])) {
                echo "<h2>You must enter the ID of the Peertube channel!</h2>";
            }
            //If this is a new channel
            elseif (!isset($_POST["id"])) {
                $channel_id = $_POST["channel_id"];

                //Nonce verification
                check_admin_referer("new_ch_peertube");

                //Insert the new channel data into the database
                $query = $wpdb->prepare(
                    "INSERT INTO " .
                        $channels_peertube_table .
                        " (`channel_id`) VALUES (%s)",
                    $channel_id
                );
                $wpdb->query($query);
            }
            //If this is an update to an existing playlist
            else {
                $channel_id = $_POST["channel_id"];
                //Nonce verification
                check_admin_referer("update_ch_peertube_" . $_POST["id"]);
                $query = $wpdb->prepare(
                    "UPDATE " .
                        $channels_peertube_table .
                        " SET `channel_id` = %s WHERE id = %d", $channel_id, (int) $_POST["id"]
                );
                
                $wpdb->query($query);
            }
        }

        //all cards are recovered
        $channels = $wpdb->get_results(
            "SELECT * FROM " . $channels_peertube_table
        );
        include plugin_dir_path(__FILE__) . "views/channels.php";
    }
}

//This function creates a Peertube playlist and allows you to display it using a shortcode in WordPress.
function playlists_peertube()
{
    //If the user is an administrator
    if (is_admin()) {
        //Global variable for database access
        global $wpdb;

        //Table to store the playlist data
        $playlist_peertube_table = $wpdb->prefix . "playlists_peertube";

        //If the form has been submitted
        if (sizeof($_POST) > 0) {
            //If the name or playlist ID fields are empty
            if (empty($_POST["name"]) || empty($_POST["playlist_id"])) {
                echo "<h2>You must enter a name and the ID of the Peertube playlist!</h2>";
            }
            //If the text size input isn't a number
            elseif (!is_numeric($_POST["text_size"])) {
                echo "<h2>Text size must be a number</h2>";
            }
            //If this is a new playlist
            elseif (!isset($_POST["id"])) {
                $playlist_id = $_POST["playlist_id"];
                //Nonce verification
                check_admin_referer("new_pl_peertube");
                $show_title = 1;
                $show_description = isset($_POST["show_description"]) ? 1 : 0;
                $desc_text_color = "#ffffff";
                $bg_color = "rgba(0, 0, 0, 0.7)";

                //Insert the new playlist data into the database
                $query = $wpdb->prepare(
                    "INSERT INTO " .
                        $playlist_peertube_table .
                        " (`name`, `playlist_id`, `template`, `text_size`, `text_color`, `desc_text_color`, `bg_color`, `show_title`, `show_description`) VALUES (%s, %s, %d, %d, %s, %s, %s, %d, %d)",
                    sanitize_text_field($_POST["name"]),
                    $_POST["playlist_id"],
                    "grid",
                    (int) $_POST["text_size"],
                    sanitize_hex_color($_POST["text_color"]),
                    $desc_text_color,
                    $bg_color,
                    $show_title,
                    $show_description
                );
                $wpdb->query($query);
            }
            //If this is an update to an existing playlist
            else {
                $playlist_id = $_POST["playlist_id"];
                //Nonce verification
                check_admin_referer("update_pl_peertube_" . $_POST["id"]);
                $show_title = isset($_POST["show_title"]) ? 1 : 0;
                $show_description = isset($_POST["show_description"]) ? 1 : 0;
                $query = $wpdb->prepare(
                    "UPDATE " .
                        $playlist_peertube_table .
                        " SET `name` = %s, `playlist_id` = %s, `template` = %d, `text_size` = %d, `text_color` = %s, `show_description` = %d WHERE id = %d",
                    sanitize_text_field($_POST["name"]),
                    $playlist_id,
                    "grid",
                    (int) $_POST["text_size"],
                    sanitize_hex_color($_POST["text_color"]),
                    $show_description,
                    (int) $_POST["id"]
                );
                
                $wpdb->query($query);
            }
        }

        //all cards are recovered
        $playlists = $wpdb->get_results(
            "SELECT * FROM " . $playlist_peertube_table
        );
        include plugin_dir_path(__FILE__) . "views/playlists.php";
    }
}

//Ajax : delete a playlist
add_action(
    "wp_ajax_remove_playlist_peertube",
    "remove_playlist_peertube_callback"
);
function remove_playlist_peertube_callback()
{
    // Check if the AJAX request came from a valid source
    check_ajax_referer("remove_playlist_peertube");

    if (is_admin()) {
        global $wpdb;

        $playlist_peertube_table = $wpdb->prefix . "playlists_peertube";

        if (is_numeric($_POST["id"])) {
            // Delete all images associated with the playlist
            $query = $wpdb->prepare(
                "DELETE FROM " .
                    $playlist_peertube_table .
                    "
                 WHERE id=%d",
                $_POST["id"]
            );
            $res = $wpdb->query($query);
        }
        wp_die();
    }
}

//Ajax : delete a channel
add_action(
    "wp_ajax_remove_channel_peertube",
    "remove_channel_peertube_callback"
);
function remove_channel_peertube_callback()
{
    // Check if the AJAX request came from a valid source
    check_ajax_referer("remove_channel_peertube");

    if (is_admin()) {
        global $wpdb;

        $channel_peertube_table = $wpdb->prefix . "channels_peertube";

        if (is_numeric($_POST["id"])) {
            // Delete all images associated with the channel
            $query = $wpdb->prepare(
                "DELETE FROM " .
                    $channel_peertube_table .
                    "
                 WHERE id=%d",
                $_POST["id"]
            );
            $res = $wpdb->query($query);
        }
        wp_die();
    }
}

// Shortcode to display a Peertube playlist
add_shortcode("player_peertube", "display_player_peertube");
function display_player_peertube()
{

    wp_enqueue_style(
        "player_peertube_grid_css",
        plugins_url("css/playerGrid.css", __FILE__)
    );

    // Load channel view file and render HTML
    $view = plugin_dir_path(__FILE__) . "views/player2.php";
    ob_start();
    include $view;
    $player_html = ob_get_clean();
    
    return $player_html;
}

// Shortcode to display a Peertube playlist
add_shortcode("channel_peertube", "display_channel_peertube");
function display_channel_peertube($atts)
{
    $peertube_url = get_option("pl_peertube_url");

    // Check if the Peertube instance URL is set
    if (empty($peertube_url)) {
        return '<strong>You need to set your Peertube instance url in the <a href="' .
            admin_url("admin.php?page=playlist-pt-settings") .
            '">settings</a> to use Peertube Playlist</strong>';
    }
    if (is_numeric($atts["id"])) {
        global $wpdb;
        $channels_peertube_table = $wpdb->prefix . "channels_peertube";
        $query = $wpdb->prepare(
            "SELECT * FROM {$channels_peertube_table} WHERE id = %d",
            $atts["id"]
        );
        $channel = $wpdb->get_row($query);
        if ($channel) {
            wp_enqueue_script("jquery");
            // Set API URL and make API request to get channel data
            $apiURL = "/api/v1";
            $url =
                $peertube_url .
                $apiURL .
                "/video-channels/" .
                $channel->channel_id .
                "";
            $response = wp_remote_get($url);
            $json = wp_remote_retrieve_body($response);

            if (!empty($json)) {
                $data = json_decode($json);
                if ($data) {
                    if (!isset($data->error) && !isset($data->errors)) {
                        // Enqueue the CSS stylesheet
                        wp_enqueue_style(
                            "channel_peertube_grid_css",
                            plugins_url("css/channel.css", __FILE__)
                        );
                        // Load channel view file and render HTML
                        $view = plugin_dir_path(__FILE__) . "views/channel.php";
                        ob_start();
                        include $view;
                        $channel_html = ob_get_clean();

                        return $channel_html;
                    } else {
                        // Handle API errors
                        $error_msg =
                            "Error retrieving channel from Peertube API on instance " .
                            $peertube_url .
                            " (you can change this in the Peertube channel settings)<br />";

                        if (!empty($data->error)) {
                            $error_msg .=
                                "API error: " . $data->error . "<br />";
                        } else {
                            $error_msg .=
                                "API errors: " .
                                print_r($data->errors, true) .
                                "<br />";
                        }

                        return $error_msg;
                    }
                } else {
                    return "Error converting JSON data";
                }
            } else {
                return "Error retrieving channel from Peertube API on instance " .
                    $peertube_url .
                    " (you can change this in the Peertube channel settings)";
            }
        } else {
            return "Error: Peertube channel with ID {$atts["id"]} not found!";
        }
    } else {
        return "Missing channel ID!";
    }
}

// Shortcode to display a Peertube playlist
add_shortcode("playlist_peertube", "display_playlist_peertube");
function display_playlist_peertube($atts)
{
    $peertube_url = get_option("pl_peertube_url");

    // Check if the Peertube instance URL is set
    if (empty($peertube_url)) {
        return '<strong>You need to set your Peertube instance url in the <a href="' .
            admin_url("admin.php?page=playlist-pt-settings") .
            '">settings</a> to use Peertube Playlist</strong>';
    }
    if (is_numeric($atts["id"])) {
        global $wpdb;
        $playlist_peertube_table = $wpdb->prefix . "playlists_peertube";
        $query = $wpdb->prepare(
            "SELECT * FROM {$playlist_peertube_table} WHERE id = %d",
            $atts["id"]
        );
        $playlist = $wpdb->get_row($query);

        if ($playlist) {
            wp_enqueue_script("jquery");
            $apiURL = "/api/v1";
            $url =
                $peertube_url .
                $apiURL .
                "/video-playlists/" .
                $playlist->playlist_id .
                "/videos";
            $response = wp_remote_get($url);
            $json = wp_remote_retrieve_body($response);

            if (!empty($json)) {
                $data = json_decode($json);

                if ($data) {
                    if (!isset($data->error) && !isset($data->errors)) {
                        wp_enqueue_style(
                            "playlist_peertube_grid_css",
                            plugins_url("css/grid.css", __FILE__)
                        );
                        $view = plugin_dir_path(__FILE__) . "views/grid.php";
                        ob_start();
                        include $view;
                        $playlist_html = ob_get_clean();
                        return $playlist_html;
                    } else {
                        $error_msg =
                            "Error retrieving playlist from Peertube API on instance " .
                            $peertube_url .
                            " (you can change this in the Peertube Playlist settings)<br />";
                        if (!empty($data->error)) {
                            $error_msg .=
                                "API error: " . $data->error . "<br />";
                        } else {
                            $error_msg .=
                                "API errors: " .
                                print_r($data->errors, true) .
                                "<br />";
                        }
                        return $error_msg;
                    }
                } else {
                    return "Error converting JSON data";
                }
            } else {
                return "Error retrieving playlist from Peertube API on instance " .
                    $peertube_url .
                    " (you can change this in the Peertube Playlist settings)";
            }
        } else {
            return "Error: Peertube playlist ID " . $atts["id"] . " not found!";
        }
    } else {
        return "Missing playlist ID!";
    }
}

add_action("wp_enqueue_scripts", function () {
    wp_enqueue_script("jquery");
});
