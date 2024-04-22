<?php

function getVideoChannelVideos($channelId) {

	if (!is_numeric($videoId) || is_null($videoId)) {
		return "Missing video ID!";
	}

	wp_enqueue_script("jquery");
	$peertube_url = get_option("pl_peertube_url");
	$apiURL = "/api/v1";
	$url = $peertube_url . $apiURL . "/videos/" . $videoId;

	$response = wp_remote_get($url);
	$json = wp_remote_retrieve_body($response);

	if (empty($json)) {
		return "Error retrieving description from Peertube API on instance " . $peertube_url . " (you can change this in the Embed Peertube settings)";
	}

	$data = json_decode($json);

	if (!$data) {
		return "Error converting JSON data";
	}

	if (isset($data->error) || isset($data->errors)) {
		$error_msg =
			"Error retrieving description from Peertube API on instance " .
			$peertube_url .
			" (you can change this in the Embed Peertube settings)<br />";
		
		if (!empty($data->error)) {
			$error_msg .= "API error: " . $data->error . "<br />";
			return $error_msg;
		} else {
			$error_msg .= "API errors: " . print_r($data->errors, true) . "<br />";
			return $error_msg;
		}
	}
	
	return $data;
}

function getPlaylist($playlistId) {
	if (!is_numeric($playlistId) || is_null($playlistId)) {
		return "Missing playlist ID!";
	}

	global $wpdb;
	$playlist_peertube_table = $wpdb->prefix . "playlists_peertube";
	$query = $wpdb->prepare(
		"SELECT * FROM {$playlist_peertube_table} WHERE id = %d",
		$playlistId
	);

	$playlist = $wpdb->get_row($query);

	if (!$playlist) {
		return "Error: Peertube playlist ID " . $playlistId . " not found!";
	}

	wp_enqueue_script("jquery");
	$peertube_url = get_option("pl_peertube_url");
	$apiURL = "/api/v1";
	$url = $peertube_url . $apiURL . "/video-playlists/" . $playlist->playlist_id . "/videos?count=100";

	$response = wp_remote_get($url);
	$json = wp_remote_retrieve_body($response);

	if (empty($json)) {
		return "Error retrieving playlist from Peertube API on instance " . $peertube_url . " (you can change this in the Embed Peertube settings)3";
	}

	$data = json_decode($json);

	if (!$data) {
		return "Error converting JSON data";
	}

	if (isset($data->error) || isset($data->errors)) {
		$error_msg =
			"Error retrieving playlist from Peertube API on instance " .
			$peertube_url .
			" (you can change this in the Peertube Playlist settings)4<br />";
		
		if (!empty($data->error)) {
			$error_msg .= "API error: " . $data->error . "<br />";
			return $error_msg;
		} else {
			$error_msg .= "API errors: " . print_r($data->errors, true) . "<br />";
			return $error_msg;
		}
	}

	return $data;
}

function getVideoDescription($videoId) {

	if (!is_numeric($videoId) || is_null($videoId)) {
		return "Missing video ID!";
	}

	wp_enqueue_script("jquery");
	$peertube_url = get_option("pl_peertube_url");
	$apiURL = "/api/v1";
	$url = $peertube_url . $apiURL . "/videos/" . $videoId;

	$response = wp_remote_get($url);
	$json = wp_remote_retrieve_body($response);

	if (empty($json)) {
		return "Error retrieving description from Peertube API on instance " . $peertube_url . " (you can change this in the Embed Peertube settings)";
	}

	$data = json_decode($json);

	if (!$data) {
		return "Error converting JSON data";
	}

	if (isset($data->error) || isset($data->errors)) {
		$error_msg =
			"Error retrieving description from Peertube API on instance " .
			$peertube_url .
			" (you can change this in the Embed Peertube settings)<br />";
		
		if (!empty($data->error)) {
			$error_msg .= "API error: " . $data->error . "<br />";
			return $error_msg;
		} else {
			$error_msg .= "API errors: " . print_r($data->errors, true) . "<br />";
			return $error_msg;
		}
	}
	
	return $data;
}

?>
