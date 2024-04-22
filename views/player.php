<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php
	require_once('peertubeAPI.php'); // Include the peertubeAPI.php file

	$show_player = false;


	$playlistId = $playlist->id;							

	if ($playlistId === null && isset($_GET['playlistId'])) {
		$playlistId = $_GET['playlistId'];
		$show_player = true;
	}

	if ($playlistId) {
		global $wpdb;

		$playlist_peertube_table = $wpdb->prefix . "playlists_peertube";

		$query = $wpdb->prepare("SELECT * FROM {$playlist_peertube_table} WHERE id = %d", $playlistId);
		$playlist = $wpdb->get_row($query);
	}

	//uuid from shortcode
	$uuid = $uuid->id;							
	//uuid from url
	if ($urlUuid === null && isset($_GET['uuid'])) {
		$urlUuid = $_GET['uuid'];
	}

	$peertube_url = get_option("pl_peertube_url");

	$data = getPlaylist($playlistId); // Call the function to retrieve the playlist data

	if (is_string($data)) {
		echo $data; // Error occurred, display error message
	} 


	echo $video_count;
	$pl_hover_delay = get_option('pl_hover_delay');
	$autoplay = $playlist->autoplay_video;
	$description_textcolor = get_option("pl_description_textcolor"); 
	$showmore_textcolor = get_option("pl_showmore_textcolor"); 

	$grid_backgroundcolor = get_option("pl_grid_backgroundcolor"); 
	$grid_textcolor = get_option("pl_grid_textcolor"); 
	$grid_textsize_header = get_option("pl_grid_textsize_header"); 
	$grid_textsize_description = get_option("pl_grid_textsize_description"); 

	$gridgap = get_option("pl_grid_gap"); 

		$peertube_playlist = array();
		$peertube_count = 0;
		$peertube_count_failed = 0;

		$reverse_array = array_reverse($data->data);

		foreach ($reverse_array as $video) {

			if (!is_null($video) && !is_null($video->video) && !is_null($video->video->name)) {
				$entry = array(
					'index' => $peertube_count,
					'uuid' => $video->video->uuid,
					'title' => str_replace("'", "\'", $video->video->name),
					'preview' => $peertube_url . $video->video->previewPath,
					'embed' => $peertube_url . $video->video->embedPath,
					'description' => str_replace(array("\r\n", "\r", "\n"), '\n', $video->video->description)
				);
				$peertube_playlist[] = $entry;
				$peertube_count++;
			} else {
				echo "Video failed: ";
				var_dump($video);
				$peertube_count_failed++;
			}
		}

		$uuid = $urlUuid ? $urlUuid : $peertube_playlist[0]['uuid'];
		$playlistName = $playlist->name;
		$showDescription = $playlist->show_description;
		$autoplay = get_option("pl_autoplay");
		$initalAutoplay = $playlist->autoplay_video;
		$scrollToVideo = $playlist->scroll_video;
		$separatePage = $playlist->click;
		$currentlyPlaying = "";
		$playlist_json = json_encode(array(
			'uuid' => $uuid,
			'playlistName' => $playlistName,
			'showDescription' => $showDescription,
			'peertubeUrl' => $peertube_url,
			'peertubePlaylist' => $peertube_playlist,
			'currentlyPlaying' => $currentlyPlaying,
			'autoplay' => $autoplay,
			'initalAutoplay' => $initalAutoplay,
			'scrollToVideo' => $scrollToVideo,
			'separatePage' => $separatePage
		));

		echo '<script>';
		echo 'window.peertubePlaylistData = ' . $playlist_json . ';';
		echo '</script>';

	$description_view = "";
	if ($playlist->show_description){
		$description_view = "
		<div class='description_view' id='description_view'>
			<p id='description_container' style='color:{$description_textcolor};'>
			
			<p id='metadata_container' style='color:{$description_textcolor};'></p>
			</p>

			<hr class='line' />
			<button id='read_more_button' onclick='toggleMetadata()' style='color:#fff; background: #4fbdc8; text-shadow: none; border: none; border-radius: 5px; padding: 10px 20px; cursor: pointer; font-size: 0.8rem;'>Mehr anzeigen</button>
		</div>";
	} 

	$controlView = "
	<div class='control_view' id='control_view'>
		<div class='video_view'>
			<div class='video_background'>
				<div class='video_format'>
					<div class='video_container_iframe' id='video_container_iframe'>
					</div>
				</div>
			</div>
			
			{$description_view}
		</div>
	</div>";

	echo $controlView; 


	if ($playlist->show_title) {
		echo "<p>".$playlist->name."</p>";
		echo "<br>";
	  }

echo "<div class='main-container'>";
echo "<div class='blx__grid__container12' style='column-gap: $gridgap px; row-gap:$gridgap px; max-columns: $max_column'>";
		

$selectedOption = get_option('pl_playbutton_style_grid');
$button_sprites = [
				'playbutton_black_grid' => 'embed-peertube-wp/images/playbutton_black.svg',
				'playbutton_white_grid' => 'embed-peertube-wp/images/playbutton_white.svg',
				'playbutton_fs1_grid' => 'embed-peertube-wp/images/playbutton_fs1.svg',
				'playbutton_fs1_2_grid' => 'embed-peertube-wp/images/playbutton_fs1_2.svg'
			];
$button_sprite_default = 'embed-peertube-wp/images/playbutton_white.svg';

$playbuttonElement = "<img class='play_grid_button' src='".plugins_url(array_key_exists($selectedOption, $button_sprites) ? $button_sprites[$selectedOption] : $button_sprite_default) . "' />";

	echo "<div class='gridContainer'>";


	foreach($reverse_array as $video)
	{
		if (is_null($video) || is_null($video->video) || is_null($video->video->name)){
			continue;
		}

		$gridVideoOnClick = "onClick='playVideo(\"{$video->video->uuid}\", \"{$autoplay}\")'";
		$videoElementHover = "
		<div class='grid_item_2' {$gridVideoOnClick} style='display: none; background:{$grid_backgroundcolor};'>
			<div class='outer_container_2'>
				<div class='tile__container_2' style='background:{$grid_backgroundcolor};'>
					<div class='tile__wrapper_2'>
						<div class='tile__content_2' style='background:{$grid_backgroundcolor}; transition-delay:${pl_hover_delay}ms;'>
							<div class='media__wrapper_2'>
								<div class='media__content_2'>
									<div class='thumbnail_container_2'>
										<img class='thumbnail_2' height='100%' width='100%' src='{$peertube_url}{$video->video->previewPath}' />
										{$playbuttonElement}
									</div>
								</div>
							</div>

							<div class='blx__title__content_2'>
								<div class='text_content' style='color:{$grid_textcolor}; font-size:{$grid_textsize_header}px;'>{$video->video->name}</div>
							</div>
							<div class='text_content_2' style='color:{$grid_textcolor}; font-size:{$grid_textsize_description}px; background:{$grid_backgroundcolor}; transition-delay:${pl_hover_delay}ms;'>
							{$video->video->description}
						</div>
						</div>
					</div>
				</div>
			</div>
		</div>";

		$videoElement2 = "
		<div class='grid_item' {$gridVideoOnClick}>
			<div class='outer_container'>
				<div class='tile__container' style='background:{$grid_backgroundcolor};'>
					<div class='tile__wrapper'>
						<div class='tile__content' style='background:{$grid_backgroundcolor}; transition-delay:${pl_hover_delay}ms;'>
							<div class='media__wrapper'>
								<div class='media__content'>
									<div class='thumbnail_container'>
										<img class='thumbnail' height='100%' width='100%' src='{$peertube_url}{$video->video->previewPath}' />
										{$playbuttonElement}
									</div>
								</div>
							</div>

							<div class='blx__title__content clamp'>
								<div class='text_content' style='color:{$grid_textcolor}; font-size:11px;'>{$video->video->name}</div>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>";

		echo $videoElement2;
		echo $videoElementHover;
	}
	echo "<div>";
	echo "</div>";
	echo "</div>";



?>