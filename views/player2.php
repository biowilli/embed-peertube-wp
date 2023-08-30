<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php
	require_once('peertubeAPI.php'); // Include the peertubeAPI.php file
	$playlistId = $_GET['playlistId'];

	$peertube_url = get_option("pl_peertube_url");

	$data = getPlaylist($playlistId); // Call the function to retrieve the playlist data

	if (is_string($data)) {
		echo $data; // Error occurred, display error message
	} 
	?> 
<div class="control_view">
	<div class="video_view">
		<div class="video_background">
		<div class="video_format">
		<div class="video_container_iframe" id="video_container_iframe">
		</div>
		</div>
		</div>
		<div class="description_view" id="description_view">
			<p id="description_container"> </p>
		</div>
	</div>

	<div class="playlist_peertube_grid" id="playlist_peertube_grid_<?= $playlistId ?>">	
<?php

//Style Grid
$pl_hover_delay = get_option('pl_hover_delay');

$grid_backgroundcolor = get_option("pl_grid_backgroundcolor"); 
$grid_textcolor = get_option("pl_grid_textcolor"); 
$grid_textsize_header = get_option("pl_grid_textsize_header"); 
$grid_textsize_description = get_option("pl_grid_textsize_description"); 

$marginTopVideo = get_option("pl_grid_margin_top"); 
$marginBottomVideo = get_option("pl_grid_margin_bottom"); 
$marginRightVideo = get_option("pl_grid_margin_right"); 
$marginLeftVideo = get_option("pl_grid_margin_left"); 

$grid_borderradius_top_left = get_option('pl_grid_borderradius_top_left');
$grid_borderradius_top_right = get_option('pl_grid_borderradius_top_right');
$grid_borderradius_bottom_left = get_option('pl_grid_borderradius_bottom_left');
$grid_borderradius_bottom_right = get_option('pl_grid_borderradius_bottom_right');

$hover_grid_borderradius_top_left = get_option('pl_hover_grid_borderradius_top_left');
$hover_grid_borderradius_top_right = get_option('pl_hover_grid_borderradius_top_right');
$hover_grid_borderradius_bottom_left = get_option('pl_hover_grid_borderradius_bottom_left');
$hover_grid_borderradius_bottom_right = get_option('pl_hover_grid_borderradius_bottom_right');

foreach($data->data as $video)
{
	//deleted or private video
	if (is_null($video) || is_null($video->video) || is_null($video->video->name)){
		continue;
	}

	// Start building the video div with the applied styles.
	echo '<div class="video" style="';
	echo 'transition-delay:' . $pl_hover_delay   . 'ms; ';
	echo 'margin-top: ' . $marginTopVideo . 'px; ';
	echo 'margin-right: ' . $marginRightVideo . 'px; ';
	echo 'margin-bottom: ' . $marginBottomVideo . 'px; ';
	echo 'margin-left: ' . $marginLeftVideo . 'px; ';
	echo 'border-top-left-radius: ' . $grid_borderradius_top_left . 'px; ';
    echo 'border-top-right-radius: ' . $grid_borderradius_top_right . 'px; ';
    echo 'border-bottom-left-radius: ' . $grid_borderradius_bottom_left . 'px; ';
    echo 'border-bottom-right-radius: ' . $grid_borderradius_bottom_right . 'px; ';
	echo '">';
	echo '<div class="video_container" rel="'.$video->video->uuid.'">';
	echo '<div class="thumbnail_container_hover" style="';
	echo 'background-color: ' . $grid_backgroundcolor . '; ';
	echo 'color: ' . $grid_textcolor . '; ';
	echo 'border-top-left-radius: ' . $hover_grid_borderradius_top_left . 'px; ';
    echo 'border-top-right-radius: ' . $hover_grid_borderradius_top_right . 'px; ';
    echo 'border-bottom-left-radius: ' . $hover_grid_borderradius_bottom_left . 'px; ';
    echo 'border-bottom-right-radius: ' . $hover_grid_borderradius_bottom_right . 'px; ';
	echo '"';
	echo 'onClick="playVideo(\'' . $video->video->uuid . '\')"'; // onClick-Event
	echo '>';
	echo '<div class="thumbnail_container">';
	echo '<img class="thumbnail" src="'.$peertube_url.$video->video->previewPath.'" />';
	$selectedOption = get_option('pl_playbutton_style_grid');

	switch ($selectedOption) {
		case 'playbutton_black_grid':
			echo '<img class="play_video" src="'.plugins_url( 'embed-peertube-playlist/images/playbutton_black.svg').'" />';
			break;

		case 'playbutton_white_grid':
			echo '<img class="play_video" src="'.plugins_url( 'embed-peertube-playlist/images/playbutton_white.svg').'" />';
			break;

		case 'playbutton_fs1_grid':
			echo '<img class="play_video" src="'.plugins_url( 'embed-peertube-playlist/images/playbutton_fs1.svg').'" />';
			break;

		case 'playbutton_fs1_2_grid':
			echo '<img class="play_video" src="'.plugins_url( 'embed-peertube-playlist/images/playbutton_fs1_2.svg').'" />';
			break;

		default:
			echo '<img class="play_video" src="'.plugins_url( 'embed-peertube-playlist/images/playbutton_white.svg').'" />';
			break;
	}

	echo '</div>';
		echo '<div class="information_container">';

			echo '<div class="header_container">';
			echo '<h3 style="color: '.$playlist->text_color.'; font-size: '.$grid_textsize_header.'px;">'.$video->video->name.'</h3>';
			echo '</div>';
		
			echo '<div class="description_container">';
			$descriptionLines = explode("\n", $video->video->description);
			$visibleLines = array_slice($descriptionLines, 0, 3);
			$visibleDescription = implode("\n", $visibleLines);

			echo '<p class="video_description" style="color: '.$playlist->text_color.'; font-size: '.$grid_textsize_description.'px;">';
			if (!empty($visibleDescription)) {
				echo $visibleDescription;
				echo '...';
			} else {
				echo 'No description';
			}
			echo '</p>';

			echo '</div>';
			echo '</div>';

	
    echo '</div>'; // Close thumbnail_container_hover
    echo '</div>'; // Close video_container
    echo '</div>'; // Close video
}

?>
</div>
	<div class="playlist_view" id="playlist_view">
		<ul class="playlist_container" id="playlist_container">
		</ul>
	</div>
<div>

<script src="https://unpkg.com/@peertube/embed-api/build/player.min.js"></script>
<script>
<?php
	$peertube_playlist = "var peertube_playlist=["; // Variable to store the Peertube playlist
	$peertube_count = 0;
	foreach($data->data as $video)
	{
		if ($peertube_count != 0) {
			$peertube_playlist .= ",";
		}

		$peertube_playlist .= "{";
		$peertube_playlist .= "index:'".$peertube_count."',"; // Video UUID		
		$peertube_playlist .= "uuid:'".$video->video->uuid."',"; // Video UUID
		$peertube_playlist .= "title:'".str_replace("'", "\'", $video->video->name )."',"; // Video title
		$peertube_playlist .= "preview:'".$peertube_url.$video->video->previewPath."',"; // Video preview image
		$peertube_playlist .= "embed:'".$peertube_url.$video->video->embedPath."',"; // Video embed link
		$description = str_replace(array("\r\n", "\r", "\n"), '\n', $video->video->description); //to handle line breaks in the description.
		$peertube_playlist .= "description:'".str_replace("'", "\'", $description)."'"; // Video description
		$peertube_playlist .= "}";
		$peertube_count += 1;
	}
	$peertube_playlist .= "];";

	echo $peertube_playlist;
?>
	var currentlyPlayingVideo = 0;
	function playVideo(uuid) {

		var targetEmbed;
		var targetIndex;

		peertube_playlist.forEach(function(video) {
			if (video.uuid === uuid) {
				targetEmbed = video.embed;
				targetDescription = video.description;
				targetIndex = video.index;
				return; 
			}
		});

		if(targetEmbed == undefined || targetIndex == undefined){
			return;
		}
		currentlyPlayingVideo = targetIndex;
        // Setze die Video-URL als src-Attribut des <iframe>-Elements 350px
		var iframe = '<iframe width="100%" height="100%" src="'+ targetEmbed + '?autoplay=1&rel=0&peertubeLink=0&api=1" frameborder="0" allowfullscreen></iframe>';

		jQuery('#video_container_iframe').html(iframe);

		// Update the browser URL without page reload
		var currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('uuid', uuid);
        history.pushState({}, '', currentUrl.toString());

		updateStyle(targetIndex);
		updateDescription(targetDescription);
		initializePlayer(targetIndex);
	}

	function updateDescription(description) {
		var discription = document.getElementById('description_container');
		discription.textContent = description;
    }

	// PeerTube >= 2.2.
	function checkPlaybackStatus(status) {
		if (status.playbackState == "ended") {
			console.log(currentlyPlayingVideo);
			if (currentlyPlayingVideo != -1) {
				var nextIndex = (currentlyPlayingVideo + 1) % peertube_playlist.length;
				var nextVideo = peertube_playlist[nextIndex];
				playVideo(nextVideo.uuid);
			}
		}
	}

	function updateStyle(selectedIndex) {
		var videos = document.querySelectorAll('.video');
		videos.forEach(function(video, index) {
			if (index != selectedIndex) {
				video.classList.add('inactive'); // Add a class to apply gray overlay
			} else {
				video.classList.remove('inactive'); // Remove the class from the playing video
			}
		});
	}

	const PeerTubePlayer = window['PeerTubePlayer']
	async function initializePlayer() {
		var iframeElement = document.querySelector('iframe');
		var player = new PeerTubePlayer(iframeElement);
		await player.ready;
		player.addEventListener("playbackStatusUpdate", checkPlaybackStatus); 
		}

    jQuery(document).ready(function(){
		var targetUuid = "<?php echo $_GET['uuid']; ?>";
		playVideo(targetUuid);
    });

</script>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
//<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
<div class="container_netflix">
  <p>Playlist</p>
<div class="swiper-container">
  <div class="swiper-wrapper">
	<?php


	foreach($data->data as $video)
{
	//deleted or private video
	if (is_null($video) || is_null($video->video) || is_null($video->video->name)){
		continue;
	}

	echo '<div class="swiper-slide" id="swiper-slide">';
	echo '<div class="swiper-box">';
	echo '<div class="video" style="';
	echo 'transition-delay:' . $pl_hover_delay   . 'ms; ';
	echo 'margin-top: ' . $marginTopVideo . 'px; ';
	echo 'margin-right: ' . $marginRightVideo . 'px; ';
	echo 'margin-bottom: ' . $marginBottomVideo . 'px; ';
	echo 'margin-left: ' . $marginLeftVideo . 'px; ';
	echo 'border-top-left-radius: ' . $grid_borderradius_top_left . 'px; ';
    echo 'border-top-right-radius: ' . $grid_borderradius_top_right . 'px; ';
    echo 'border-bottom-left-radius: ' . $grid_borderradius_bottom_left . 'px; ';
    echo 'border-bottom-right-radius: ' . $grid_borderradius_bottom_right . 'px; ';
	echo '">';
	echo '<div class="video_container" rel="'.$video->video->uuid.'">';
	echo '<div class="thumbnail_container_hover" style="';
	echo 'background-color: ' . $grid_backgroundcolor . '; ';
	echo 'color: ' . $grid_textcolor . '; ';
	echo 'border-top-left-radius: ' . $hover_grid_borderradius_top_left . 'px; ';
    echo 'border-top-right-radius: ' . $hover_grid_borderradius_top_right . 'px; ';
    echo 'border-bottom-left-radius: ' . $hover_grid_borderradius_bottom_left . 'px; ';
    echo 'border-bottom-right-radius: ' . $hover_grid_borderradius_bottom_right . 'px; ';
	echo '"';
	echo 'onClick="playVideo(\'' . $video->video->uuid . '\')"'; // onClick-Event
	echo '>';
	echo '<div class="thumbnail_container">';

	echo '<img class="thumbnail" src="'.$peertube_url.$video->video->previewPath.'" alt="" />';
	echo '<div class="information_container">';
	echo '<div class="header_container">';
	echo '<h3 style="color: '.$playlist->text_color.'; font-size: '.$grid_textsize_header.'px;">'.$video->video->name.'</h3>';
	echo '</div>';
	echo '<div class="description_container">';

	$descriptionLines = explode("\n", $video->video->description);
	$visibleLines = array_slice($descriptionLines, 0, 3);
	$visibleDescription = implode("\n", $visibleLines);

	echo '<p class="video_description" style="color: '.$playlist->text_color.'; font-size: '.$grid_textsize_description.'px;">';
	if (!empty($visibleDescription)) {
		echo $visibleDescription;
		echo '...';
	} else {
		echo 'No description';
	}
	echo '</p>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	
	echo '</div>';
  	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
}?>
</div>

</div>

</div>


  <script>
	const swiper = new Swiper(".swiper-container", {
	slidesPerView: 2,
	slidesPerGroup: 1,
	centeredSlides: true,
	loop: true,
	navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},
	breakpoints: {
		// when window width is >= 600px
		600: {
		slidesPerView: 2,
		slidesPerGroup: 2,
		spaceBetween: 5,
		centeredSlides: true
		
		},
		// when window width is >= 900px
		900: {
		slidesPerView: 3,
		slidesPerGroup: 3,
		spaceBetween: 5,
		centeredSlides: false
		
		},
		// when window width is >= 1200px
		1200: {
		slidesPerView: 4,
		slidesPerGroup: 4,
		spaceBetween: 5,
		centeredSlides: false
		},
		
		// when window width is >= 1500px
		1500: {
		slidesPerView: 5,
		slidesPerGroup: 5,
		spaceBetween: 5,
		centeredSlides: false
		},
		
		// when window width is >= 1800px
		1800: {
		slidesPerView: 6,
		slidesPerGroup: 6,
		spaceBetween: 5,
		centeredSlides: false
		}
	}
	});

</script>


