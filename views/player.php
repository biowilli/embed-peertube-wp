<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php
	require_once('peertubeAPI.php'); // Include the peertubeAPI.php file
	//request für playlist in die Datenbank erst hier was fehlt?

	abstract class VIDEOLOCATION {
		const SAMEPAGE = "0";
		const SEPARATE = "1";
	}

	abstract class PLAYLISTTEMPLATE {
		const GRID = "0";
		const SLIDER = "1";
	}
	$show_player = false;
	$playlistId = $playlist->id; //check if u get playlistId from here

	if ($playlistId === null) {
		$playlistId = $_GET['playlistId'];	

		if ($playlistId) {

			global $wpdb;

			$playlist_peertube_table = $wpdb->prefix . "playlists_peertube";

			$query = $wpdb->prepare(
				"SELECT * FROM {$playlist_peertube_table} WHERE id = %d",
				$playlistId
			);
			$playlist = $wpdb->get_row($query);
			$show_player = true;
	} 
}

	$peertube_url = get_option("pl_peertube_url");

	$data = getPlaylist($playlistId); // Call the function to retrieve the playlist data

	if (is_string($data)) {
		echo $data; // Error occurred, display error message
	} 

	//Style Grid
	$pl_hover_delay = get_option('pl_hover_delay');

	$autoplay = get_option("pl_autoplay"); 
	$description_textcolor = get_option("pl_description_textcolor"); 
	$showmore_textcolor = get_option("pl_showmore_textcolor"); 

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
	?> 

<?php
if ($playlist->click === VIDEOLOCATION::SAMEPAGE || $show_player) {
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
			<p id="description_container" style="color: <?php echo $description_textcolor; ?>;"></p>
			<hr class="line" />
			<button id="read_more_button" onclick="toggleDescription()" style="color: <?php echo $pl_showmore_textcolor; ?>;">Mehr anzeigen</button>
		</div>

	</div>
</div>
	<?php }?>
	<?php
	
if ($playlist->template === PLAYLISTTEMPLATE::GRID) {
	if ($playlist->show_title) {
		echo "<p>".$playlist->name."</p>";
		echo "<br>";
	  } }?>

	<div class="playlist_peertube_grid" id="playlist_peertube_grid_<?= $playlistId ?>">	
<?php
if ($playlist->template === "0") {

	foreach($data->data as $video)
	{
		//deleted or private video
		if (is_null($video) || is_null($video->video) || is_null($video->video->name)){
			continue;
		}

		// videoElement open
		$videoElement = "
		<div
			class='video'
			style='transition-delay:${pl_hover_delay}ms; transition-delay:${pl_hover_delay}ms; margin-top:${marginTopVideo}px; margin-right:${marginRightVideo}px; margin-bottom:${marginBottomVideo}px; margin-left:${marginLeftVideo}px; border-top-left-radius:${grid_borderradius_top_left}px; border-top-right-radius:${grid_borderradius_top_right}px; border-bottom-left-radius:${grid_borderradius_bottom_left}px; border-bottom-right-radius:${grid_borderradius_bottom_right}px;'
			>
		";
		echo $videoElement;

			//video_container open
			echo '<div class="video_container" rel="'.$video->video->uuid.'">';
				$thumbnailContainerHoverElement = "
				<div
					class='thumbnail_container_hover' 
					style='background-color:{$grid_backgroundcolor}; color:{$grid_textcolor}; border-top-left-radius:{$hover_grid_borderradius_top_left}px; border-top-right-radius:{$hover_grid_borderradius_top_right}px; border-bottom-left-radius:{$hover_grid_borderradius_bottom_left}px; border-bottom-right-radius: {$hover_grid_borderradius_bottom_right}px;'";
					if ($playlist->click === VIDEOLOCATION::SAMEPAGE || $show_player) {
						$thumbnailContainerHoverElement .= " onClick='playVideo(\"{$video->video->uuid}\", \"{$autoplay}\")'";
					} elseif ($playlist->click === VIDEOLOCATION::SEPARATE && !$show_player) {
						$thumbnailContainerHoverElement .= " onClick='playVideoInSeparatePage(\"{$video->video->uuid}\")'";
					}
					
					$thumbnailContainerHoverElement .= ">";
					echo $thumbnailContainerHoverElement;

						echo '<div class="thumbnail_container">';
							echo '<img class="thumbnail" src="'.$peertube_url.$video->video->previewPath.'" />';
					
							$selectedOption = get_option('pl_playbutton_style_grid');
							$button_sprites = [
								'playbutton_black_grid' => 'embed-peertube-wp/images/playbutton_black.svg',
								'playbutton_white_grid' => 'embed-peertube-wp/images/playbutton_white.svg',
								'playbutton_fs1_grid' => 'embed-peertube-wp/images/playbutton_fs1.svg',
								'playbutton_fs1_2_grid' => 'embed-peertube-wp/images/playbutton_fs1_2.svg'
							];
							$button_sprite_default = 'embed-peertube-wp/images/playbutton_white.svg';

							$playbuttonElement = "<img class='play_grid_button' src='".plugins_url(array_key_exists($selectedOption, $button_sprites) ? $button_sprites[$selectedOption] : $button_sprite_default) . "' />";
							echo $playbuttonElement;
					
						echo '</div>'; //thumbnail_container close


					echo '<div class="information_container">';
						$informationContainerELement = "<div class='header_container'>
															<h3 style='color:{$grid_textcolor}; font-size:{$grid_textsize_header}px;'>{$video->video->name}</h3>
														</div>";
						echo $informationContainerELement;
					
						echo '<div class="description_container">';
							echo '<p class="video_description" style="color: '.$grid_textcolor.'; font-size: '.$grid_textsize_description.'px;">';
							echo $video->video->description;
							echo '</p>'; 
						echo '</div>'; //close description_container 
					echo '</div>'; //close information_container
				echo '</div>'; // Close thumbnail_container_hover
			echo '</div>'; // Close video_container
		echo '</div>'; // Close videoElement
	}
}
?>
</div>
<script>
	document.addEventListener("DOMContentLoaded", function() {
	var ellipsis = Ellipsis({
          lines: 1
        });
		console.log("ellipsis", ellipsis);
	var elements = document.getElementsByClassName('video_description');
	ellipsis.add(elements);
  });

</script>


<script src="https://unpkg.com/@peertube/embed-api/build/player.min.js"></script>
<script>

function toggleDescription() {
    var container = document.getElementById("description_container");
    var button = document.getElementById("read_more_button");

    if (container.style.maxHeight) {
        container.style.maxHeight = null;
        button.innerHTML = "Mehr anzeigen";
    } else {
        container.style.maxHeight = container.scrollHeight + "px";
        button.innerHTML = "Weniger anzeigen";
    }
}


<?php
	$peertube_playlist = "var peertube_playlist=["; // Variable to store the Peertube playlist
	$peertube_count = 0;
	foreach($data->data as $video)
	{
		//deleted or private video
		if (is_null($video) || is_null($video->video) || is_null($video->video->name)){
			continue;
		}

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
	function playVideoInSeparatePage(uuid){
		var siteUrl = '<?php echo site_url(); ?>';
        // Redirect to the separate page with the video URL as a parameter
        window.location.href = siteUrl + '/index.php/playlist/?uuid=' + encodeURIComponent(uuid)+ '&playlistId=' + encodeURIComponent(<?= $playlist->id  ?>) ;
	}

	var currentlyPlayingVideo = 0;
	function playVideo(uuid, autoplay) {

		var targetEmbed;
		var targetIndex;
		var targetUuid;

		peertube_playlist.forEach(function(video) {
			if (video.uuid === uuid) {
				targetUuid = video.uuid;
				targetEmbed = video.embed;
				targetDescription = video.description;
				targetIndex = video.index;
				return; 
			}
		});

		if(targetEmbed == undefined || targetIndex == undefined || targetUuid == undefined){
			return;
		}

		currentlyPlayingVideo = targetIndex;
        // Setze die Video-URL als src-Attribut des <iframe>-Elements 350px
		var iframe = '<iframe width="100%" height="100%" src="'+ targetEmbed + '?autoplay='+ autoplay + '&rel=0&peertubeLink=0&api=1" frameborder="0" allowfullscreen></iframe>';

		// scroll 2 iframe/player
		var iframeElement = document.getElementById('video_container_iframe');
		if (iframeElement) {
			window.scrollTo({
				top: iframeElement.offsetTop,
				behavior: 'smooth'
			});
		}

		jQuery('#video_container_iframe').html(iframe);

		// Update the browser URL without page reload
		var currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('uuid', uuid);
        history.pushState({}, '', currentUrl.toString());

		updateStyle(targetIndex);

		updateMetadata(targetUuid);
		initializePlayer(targetIndex, autoplay);
	}

	function updateMetadata(targetUuid) {
		var metadata = <?php echo "test"?>

		var description = document.getElementById('description_container');
		description.textContent = targetDescription;
    }

	function checkPlaybackStatus(status) {
		if (status.playbackState == "ended") {
			if (currentlyPlayingVideo != -1) {
				var nextIndex = (currentlyPlayingVideo + 1) % peertube_playlist.length;
				var nextVideo = peertube_playlist[nextIndex];
				console.info("play new video: ", nextVideo.uuid)
				var autoplay = <?php
					echo get_option("pl_autoplay"); 
				?>;
				playVideo(nextVideo.uuid, autoplay);
			}
		}
	}

	function updateStyle(selectedIndex) {
		var videos = document.querySelectorAll('.video');
		var playGridButton = document.querySelector('.play_grid_button');

		videos.forEach(function(video, index) {
			if (index != selectedIndex) {
				video.classList.add('inactive'); 		// Remove the class from the playing video
			} else {
				video.classList.remove('inactive'); 	// Add a class to apply gray overlay
			}
		});
	}

	const PeerTubePlayer = window['PeerTubePlayer']
	async function initializePlayer() {
		console.info("initializing Player")
		var iframeElement = document.querySelector('iframe');
		var player = new PeerTubePlayer(iframeElement);
		await player.ready;
		console.info("Player ready");
		player.addEventListener("playbackStatusUpdate", checkPlaybackStatus); 
		}

    jQuery(document).ready(function(){

		var targetUuid = "<?php
			if (isset($_GET['uuid'])) {
				echo $_GET['uuid'];
			} else {
				echo "";
			}
		?>";

		var autoplay = <?php
			echo get_option("pl_autoplay"); 
		?>;

		console.info("jQuery ready start player with ID:", targetUuid);
		if (targetUuid === ""){
			playVideo(peertube_playlist[0].uuid, autoplay);
			return;
		}
		
		playVideo(targetUuid, autoplay);
    });
</script>

<?php 
if ($playlist->template === PLAYLISTTEMPLATE::SLIDER) {
	?>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<!--- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" /> --->
<div class="container_silder">
  <p><?php 
  if ($playlist->show_title) {
	echo $playlist->name; 
  }
 ?></p>
<div class="swiper-container">
  <div class="swiper-wrapper">
	<?php 
	foreach($data->data as $video)
	{
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
	if ($playlist->click === VIDEOLOCATION::SAMEPAGE || $show_player){
		echo 'onClick="playVideo(\'' . $video->video->uuid . '\',\'' . $autoplay . '\')"';
	}

	if ($playlist->click === VIDEOLOCATION::SEPARATE && !$show_player){
		echo 'onClick="playVideoInSeparatePage(\'' . $video->video->uuid . '\')"';
	}

	echo '>';
	echo '<div class="thumbnail_container">';

	echo '<img class="thumbnail" src="'.$peertube_url.$video->video->previewPath.'" alt="" />';
	echo '<div class="information_container">';
	echo '<div class="header_container">';
	echo '<h3 style="color: '.$grid_textcolor.'; font-size: '.$grid_textsize_header.'px;">'.$video->video->name.'</h3>';
	echo '</div>';
	echo '<div class="description_container">';


	$descriptionLines = explode("\n", $video->video->description);
	$visibleLines = array_slice($descriptionLines, 0, 3);
	$visibleDescription = implode("\n", $visibleLines);

	echo '<p class="video_description" style="color: '.$grid_textcolor.'; font-size: '.$grid_textsize_description.'px;">';
	echo $visibleDescriptionn;

/*
	if (!empty($visibleDescription)) {
		echo $visibleDescription;
		echo '...';
	} else {
		echo 'No description';
	}*/
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
	centeredSlides: false,
	loop: false,
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
<?php }?>