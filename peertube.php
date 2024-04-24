<?php

/*
Plugin Name: Embed-Peertube-Wp
Plugin URI: 
Version: 2.00
Description: Display Peertube Playlists and Channels in WP
Author: Monz Philipp
Author URI: https://www.fairkom.com/en/shop
Network: false
Text Domain: embed-peertube-wp
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
    $livestream_peertube_table = $wpdb->prefix . "livestream_peertube";

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
            click int(1) NOT NULL,     
			template int(11) NOT NULL,
            show_title int(1) NOT NULL,           
            show_description int(1) NOT NULL,      
            scroll_video int(1) NOT NULL,      
            autoplay_video int(1) NOT NULL,      
			PRIMARY KEY  (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
	";

    // Create SQL query for playlist table
    $livestream_sql =
        "
		CREATE TABLE `" .
        $livestream_peertube_table .
        "` (
			id int(11) NOT NULL AUTO_INCREMENT,
			livestream_id varchar(50) NOT NULL,
			PRIMARY KEY  (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
	";

    // Execute the queries
    dbDelta($channel_sql);
    dbDelta($playlist_sql);
    dbDelta($livestream_sql);
}

function remove_peertube_desinstall()
{
    global $wpdb;

    // Define table names
    $playlist_peertube_table = $wpdb->prefix . "playlists_peertube";
    $channels_peertube_table = $wpdb->prefix . "channels_peertube";
    $livestream_peertube_table = $wpdb->prefix . "livestream_peertube";

    // Create SQL queries to drop the tables
    $playlist_sql = "DROP TABLE " . $playlist_peertube_table;
    $channels_sql = "DROP TABLE " . $channels_peertube_table;
    $livestream_sql = "DROP TABLE " . $channels_peertube_table;

    // Execute the queries
    $wpdb->query($playlist_sql);
    $wpdb->query($channels_sql);
    $wpdb->query($livestream_sql);
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
        "Manage Livestreams",
        "Livestreams",
        "manage_options",
        "livestream-pt",
        "livestreams_peertube"
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
    add_option('pl_autoplay', 1);
    add_option('pl_description_textcolor', '#FFFFFF');
    add_option('pl_showmore_textcolor', '#FFFFFF');

    add_option('pl_grid_backgrondcolor', '#575757');
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

    add_option("pl_grid_gap", 0); 
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

        update_option('pl_autoplay', absint($_POST['pl_autoplay']));
        update_option('pl_showmore_textcolor', sanitize_text_field($_POST['pl_showmore_textcolor']));
        update_option('pl_description_textcolor', sanitize_text_field($_POST['pl_description_textcolor']));
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

        update_option('pl_grid_gap', absint($_POST['pl_grid_gap']));

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
function livestreams_peertube()
{

    //If the user is an administrator
    if (is_admin()) {
        //Global variable for database access
        global $wpdb;

        //Table to store the channel data
        $livestream_peertube_table = $wpdb->prefix . "livestream_peertube";
        //If the form has been submitted
        if (sizeof($_POST) > 0) {
            //If the name or channel ID fields are empty
            if (empty($_POST["livestream_id"])) {
                echo "<h2>You must enter the ID of the Peertube Livestream!</h2>";
            } elseif (!isset($_POST["id"])) {
                $livestream_id = $_POST["livestream_id"];

                //Nonce verification
                check_admin_referer("new_livestream_peertube");

                //Insert the new channel data into the database
                $query = $wpdb->prepare(
                    "INSERT INTO " .
                        $livestream_peertube_table .
                        " (`livestream_id`) VALUES (%s)",
                    $livestream_id
                );
                $wpdb->query($query);
            } else {
                $livestream_id = $_POST["livestream_id"];
                //Nonce verification
                check_admin_referer("update_livestream_peertube_" . $_POST["id"]);
                $query = $wpdb->prepare(
                    "UPDATE " .
                        $livestream_peertube_table .
                        " SET `livestream_id` = %s WHERE id = %d", $livestream_id, (int) $_POST["id"]
                );
                
                $wpdb->query($query);
            }
        }

        //all cards are recovered
        $livestreams = $wpdb->get_results(
            "SELECT * FROM " . $livestream_peertube_table
        );

        include plugin_dir_path(__FILE__) . "views/livestreams.php";
    }
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
        //If the form has been submitted
        if (sizeof($_POST) > 0) {
            //If the name or channel ID fields are empty
            if (empty($_POST["channel_id"])) {
                echo "<h2>You must enter the ID of the Peertube channel!</h2>";
            } elseif (!isset($_POST["id"])) {
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
            } else {
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
            } elseif (!isset($_POST["id"])) {
                $playlist_id = $_POST["playlist_id"];
                //Nonce verification
                check_admin_referer("new_pl_peertube");
                //Insert the new playlist data into the database
                $query = $wpdb->prepare(
                    "INSERT INTO " .
                        $playlist_peertube_table .
                        " (`name`, `playlist_id`, `click`, `template`, `show_title`, `autoplay_video`, `scroll_video`, `show_description`) VALUES (%s, %s, %d, %d, %d, %d, %d, %d)",
                    sanitize_text_field($_POST["name"]),
                    $_POST["playlist_id"],
                    (int) $_POST["click"],      
                    (int) $_POST["template"],        
                    (int) $_POST["show_title"],
                    (int) $_POST["autoplay_video"],      
                    (int) $_POST["scroll_video"],        
                    (int) $_POST["show_description"]
                );
                $wpdb->query($query);
                
            } else {
                $playlist_id = $_POST["playlist_id"];
                $click = $_POST["click"];
                $template = $_POST["template"];
                $show_title = $_POST["show_title"];
                $autoplay_video = $_POST["autoplay_video"];
                $scroll_video = $_POST["scroll_video"];
                $show_description = $_POST["show_description"];

                //Nonce verification
                check_admin_referer("update_pl_peertube_" . $_POST["id"]);
                $query = $wpdb->prepare(
                    "UPDATE " .
                    $playlist_peertube_table .
                    " SET `name` = %s, `playlist_id` = %s, `click` = %d,  `template` = %d, `show_title` = %d, `autoplay_video` = %d, `scroll_video` = %d, `show_description` = %d  WHERE id = %d",
                    sanitize_text_field($_POST["name"]),
                    $playlist_id,
                    $click,
                    $template,
                    $show_title,
                    $autoplay_video,
                    $scroll_video, 
                    $show_description,
                    $_POST["id"]
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

//Ajax : delete a livestreams
add_action(
    "wp_ajax_remove_livestream_peertube",
    "remove_livestream_peertube_callback"
);
function remove_livestream_peertube_callback()
{
    // Check if the AJAX request came from a valid source
    check_ajax_referer("remove_livestream_peertube");

    if (is_admin()) {
        global $wpdb;

        $livestreams_peertube_table = $wpdb->prefix . "livestream_peertube";

        if (is_numeric($_POST["id"])) {
            // Delete all images associated with the channel
            $query = $wpdb->prepare(
                "DELETE FROM " .
                    $livestreams_peertube_table .
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

// Shortcode to display a Peertube playlist Player
add_shortcode("player_peertube", "display_player_peertube2");
function display_player_peertube2($atts) 
{
    $peertube_url = get_option("pl_peertube_url");
    wp_enqueue_style("player_peertube_grid_css", plugins_url("css/playerGrid.css", __FILE__));

    // Load channel view file and render HTML
    $view = plugin_dir_path(__FILE__) . "views/player.php";
    ob_start();
    include $view;
    $player_html = ob_get_clean();
                
    return $player_html;
}



add_shortcode("playlist_player_peertube", "display_player_peertube");
function display_player_peertube($atts) 
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
                            "player_peertube_grid_css",
                            plugins_url("css/playerGrid.css", __FILE__)
                        );

                // Load channel view file and render HTML
                $view = plugin_dir_path(__FILE__) . "views/player.php";
                ob_start();
                include $view;
                $player_html = ob_get_clean();
                
                return $player_html;
                
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
                    " (you can change this in the Peertube Playlist settings)2";
            }
        } else {
            return "Error: Peertube playlist ID " . $atts["id"] . " not found!";
        }
    } else {
        return "Missing playlist ID!";
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
                    " (you can change this in the Peertube Playlist settings)2";
            }
        } else {
            return "Error: Peertube playlist ID " . $atts["id"] . " not found!";
        }
    } else {
        return "Missing playlist ID!";
    }
}

// Shortcode to display a Peertube channel
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

// Shortcode to display a Peertube livestream
add_shortcode("livestream_peertube", "display_livestream_peertube");
function display_livestream_peertube($atts)
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
        $livestreams_peertube_table = $wpdb->prefix . "livestream_peertube";
        $query = $wpdb->prepare(
            "SELECT * FROM {$livestreams_peertube_table} WHERE id = %d",
            $atts["id"]
        );
        $livestream = $wpdb->get_row($query);
        if ($livestream) {
            wp_enqueue_script("jquery");
            
            if (!empty($livestream->livestream_id)) {
                
                                        // Enqueue the CSS stylesheet
                                        wp_enqueue_style(
                                            "livestream_peertube_grid_css",
                                            plugins_url("css/livestream.css", __FILE__)
                                        );
                                        
                                        // Load channel view file and render HTML
                                        $view = plugin_dir_path(__FILE__) . "views/livestream.php";
                                        ob_start();
                                        include $view;
                                        $livestream_html = ob_get_clean();

                                        return $livestream_html;
                                    }
                                    else {
                                        return "Missing livestream ID!";
                                    }
              
    } else {
        return "Missing livestream ID!";
    }
}
}

add_action("wp_enqueue_scripts", function () {
    wp_enqueue_script("jquery");

    wp_enqueue_script('peertube-player-script', 'https://unpkg.com/@peertube/embed-api/build/player.min.js', array(), null, true);
    wp_enqueue_script('peertube-player-script-local', plugin_dir_url(__FILE__) . 'views/js/player.js', array(), null, true);
    wp_enqueue_script('peertube-playlist-script-local', plugin_dir_url(__FILE__) . 'views/js/playlist.js', array(), null, true);
});
