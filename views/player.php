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

	//playlist from shortcode
	$playlistId = $playlist->id;							
	//playlist from url
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

	//Style Grid
	$pl_hover_delay = get_option('pl_hover_delay');

	$autoplay = $playlist->autoplay_video; // get_option("pl_autoplay"); 
	$description_textcolor = get_option("pl_description_textcolor"); 
	$showmore_textcolor = get_option("pl_showmore_textcolor"); 

	$grid_backgroundcolor = get_option("pl_grid_backgroundcolor"); 
	$grid_textcolor = get_option("pl_grid_textcolor"); 
	$grid_textsize_header = get_option("pl_grid_textsize_header"); 
	$grid_textsize_description = get_option("pl_grid_textsize_description"); 


	$gridgap = get_option("pl_grid_gap"); 
	?> 

<?php
if ($playlist->click === VIDEOLOCATION::SAMEPAGE || $show_player) {

	$description_view = "";
	if ($playlist->show_description){
		$description_view = "
		<div class='description_view' id='description_view'>
			<p id='description_container' style='color:{$description_textcolor};'></p>
			<p id='metadata_container' style='color:{$description_textcolor};'></p>
			<hr class='line' />
			<button id='read_more_button' onclick='toggleDescription()' style='color:{$showmore_textcolor};'>Mehr anzeigen</button>
		</div>";
	}

	$controlView = "
	<div class='control_view'>
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
}?>
	<?php
if ($playlist->template === PLAYLISTTEMPLATE::GRID) {
	if ($playlist->show_title) {
		echo "<p>".$playlist->name."</p>";
		echo "<br>";
	  } }?>

<div class="main-container">
		<div class="blx__grid__container" style="column-gap:<?php echo $gridgap ?>px; row-gap:<?php echo $gridgap ?>px; max-columns:<?php echo $max_column ?>">
		
<?php
$selectedOption = get_option('pl_playbutton_style_grid');
$button_sprites = [
				'playbutton_black_grid' => 'embed-peertube-wp/images/playbutton_black.svg',
				'playbutton_white_grid' => 'embed-peertube-wp/images/playbutton_white.svg',
				'playbutton_fs1_grid' => 'embed-peertube-wp/images/playbutton_fs1.svg',
				'playbutton_fs1_2_grid' => 'embed-peertube-wp/images/playbutton_fs1_2.svg'
			];
$button_sprite_default = 'embed-peertube-wp/images/playbutton_white.svg';

$playbuttonElement = "<img class='play_grid_button' src='".plugins_url(array_key_exists($selectedOption, $button_sprites) ? $button_sprites[$selectedOption] : $button_sprite_default) . "' />";

if ($playlist->template === "0") {

	foreach($data->data as $video)
	{
		//deleted or private video
		if (is_null($video) || is_null($video->video) || is_null($video->video->name)){
			continue;
		}

		$gridVideoOnClick = "";
		if ($playlist->click === VIDEOLOCATION::SAMEPAGE || $show_player) {
			$gridVideoOnClick .= " onClick='playVideo(\"{$video->video->uuid}\", \"{$autoplay}\")'";
		} elseif ($playlist->click === VIDEOLOCATION::SEPARATE && !$show_player) {
			$gridVideoOnClick .= " onClick='playVideoInSeparatePage(\"{$video->video->uuid}\")'";
		}
		$videoElement = "
		<div class='blx__grid__item' {$gridVideoOnClick} >
			<div class='blx__tile__outer_container'>
				<div class='blx__tile__container' style='background:{$grid_backgroundcolor};'>
					<div class='blx__tile__wrapper'>
						<div class='blx__tile__content' style='background:{$grid_backgroundcolor}; transition-delay:${pl_hover_delay}ms; transition-delay:${pl_hover_delay}ms;'>
							<div class='blx__media__wrapper'>
								<div class='blx__media__content'>
									<div class='thumbnail_container'>
										<img height='100%' width='100%' src='{$peertube_url}{$video->video->previewPath}' />
										{$playbuttonElement}
									</div>
								</div>
							</div>

							<div class='blx__title__content clamp'>
								<div class='blx_text_content' style='color:{$grid_textcolor}; font-size:{$grid_textsize_header}px;'>{$video->video->name}</div>
							</div>
						</div>

						<div class='blx__tile__drawer' style='color:{$grid_textcolor}; font-size:{$grid_textsize_description}px; background:{$grid_backgroundcolor}; transition-delay:${pl_hover_delay}ms; transition-delay:${pl_hover_delay}ms;'>
							{$video->video->description}
						</div>
					</div>
				</div>
			</div>
		</div>
			";
		echo $videoElement;
	}
}
?>
</div>
</div> 
</div>
<script>

		const dStore = new Map([
            ['resize_lock', false],
			['resize_value', { width: 0, height: 0 }],
            ['elements', []],
            ['elements_map', new Map()]
        ]);
		// Get all elements with class 'clamp' and store them in a Map
		for (const element of document.getElementsByClassName('clamp')) {
			const rects = element.getClientRects();
			const child = element.firstElementChild;

			const obj = {
				element: element,
				child: child,
				content: child.innerHTML,
				height: rects[0].height
			};
			dStore.get('elements').push(obj);
			dStore.get('elements_map').set(element, obj);
		}
		const re_removeLastWord = new RegExp('[\s\n\r]+[^\s\n\r]+[\s\n\r]*$', 'i');

		// Ellipsify text
		const ellipsify = () => {
			for (const obj of dStore.get('elements')) {
				let content = obj.content;
				obj.child.innerHTML = content;

				let rect = obj.child.getClientRects()[0];
				while (rect.height > obj.height) {
					content = content.replace(re_removeLastWord, '');
					obj.child.innerHTML = content + '...';
					rect = obj.child.getClientRects()[0];
				}
			}
		}

		// Throttle resize events
		const throttleResizeEvents = (evtType) => {
            if (!dStore.get('resize_lock')) {
                dStore.set('resize_lock', true);
                window.requestAnimationFrame(() => {
					// Ellipsify text
					ellipsify();

					// Dispatch throttled resize event
                    window.dispatchEvent(new CustomEvent('blx-resize', {
                        detail: {
                            size: dStore.get('resize_value')
                        }
                    }));
                    dStore.set('resize_lock', false);
                });
            }
        };
		// Add event listener for resize event
        window.addEventListener('resize', (evt) => {
            dStore.set('resize_value', {
                width: window.innerWidth,
                height: window.innerHeight
            });
            throttleResizeEvents('resize');
        });

		// Initial ellipsifying of text
		ellipsify();

	</script>
</div>
<script src="https://unpkg.com/@peertube/embed-api/build/player.min.js"></script>
<script>
		console.log("failed expression");

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
		$peertube_playlist .= "index:'".$peertube_count."',"; // Video index		
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
    echo "var uuid = " . ($urlUuid ? "'" . $urlUuid . "'" : "peertube_playlist[0].uuid") . ";"; // If no UUID is present, use the first UUID in the array
    echo "console.log('videouuid:', uuid);";

    echo "var autoplay = " . get_option("pl_autoplay") . ";";
    echo "console.log('general autoplay from settings:', autoplay);";

    echo "var initalAutoplay = " . $playlist->autoplay_video . ";";
    echo "console.log('autoplay_video inital:', initalAutoplay);";

	echo "var scrollToVideo = " . $playlist->scroll_video . ";";
    echo "console.log('scrollToVideo inital:', scrollToVideo);";
?>

	var currentlyPlayingVideo = 0;

	function playVideoInSeparatePage(uuid) {
		var siteUrl = '<?php echo site_url(); ?>';
		// Redirect to the separate page with the video URL as a parameter
		window.location.href = siteUrl + '/index.php/playlist/?uuid=' + encodeURIComponent(uuid) + '&playlistId=' + encodeURIComponent('<?php echo $playlist->id?>');
		
		//TODO Update the browser URL only when its displayed on a seperate page
		/*
			var currentUrl = new URL(window.location.href);
			currentUrl.searchParams.set('uuid', uuid);
			history.pushState({}, '', currentUrl.toString());
			*/
	}

	function playVideo(uuid, autoplay, inital = false) {

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

		if (targetEmbed == undefined || targetIndex == undefined || targetUuid == undefined) {
			return;
		}

		currentlyPlayingVideo = targetIndex;
		// Setze die Video-URL als src-Attribut des <iframe>-Elements 350px
		var iframe = '<iframe width="100%" height="100%" src="' + targetEmbed + '?autoplay=' + autoplay + '&rel=0&peertubeLink=0&api=1" frameborder="0" allowfullscreen></iframe>';

		// scroll 2 iframe/player
		if (scrollToVideo && !inital) {
			var iframeElement = document.getElementById('video_container_iframe');
			if (iframeElement) {
				window.scrollTo({
					top: iframeElement.offsetTop,
					behavior: 'smooth'
				});
			}
		}



		jQuery('#video_container_iframe').html(iframe);

		updateStyle(targetIndex);
		updateDescription(targetDescription);
		updateMetadata(targetUuid);
		//initializePlayer(targetIndex, autoplay);
	}

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

	function updateDescription(targetDescription) {
		var videoDescriptionContainer = document.getElementById('description_container');
		if (videoDescriptionContainer && targetDescription) {
			console.info("update Description from Peertube:", targetDescription);
			videoDescriptionContainer.textContent = targetDescription;
		}
	}

	function updateMetadata(targetUuid) {
		//TODO Request Metadata
		var targetMetadata = undefined;
		//fetch();
		var metadataContainer = document.getElementById('metadata_container');
		if (metadataContainer && targetMetadata) {
			console.info("update metadata from Peertube with UUID:", targetUuid);
			metadataContainer.textContent = "";
		}
	}

	function checkPlaybackStatus(status) {
		if (status.playbackState == "ended") {
			if (currentlyPlayingVideo != -1) {
				var nextIndex = (currentlyPlayingVideo + 1) % peertube_playlist.length;
				var nextVideo = peertube_playlist[nextIndex];
				console.info("play new video: ", nextVideo.uuid)
				playVideo(nextVideo.uuid, autoplay);
			}
		}
	}

	function updateStyle(selectedIndex) {
		var videos = document.querySelectorAll('.blx__tile__outer_container');
		videos.forEach(function(video, index) {
			if (index == selectedIndex) {
				video.style.filter = "grayscale(100%)";
				video.querySelector('.play_grid_button').style.display = 'block';
			} else {
				video.style.filter = "none";
				video.querySelector('.play_grid_button').style.display = 'none';
			}
		});
	}

	const PeerTubePlayer = window['PeerTubePlayer'];
	async function initializePlayer() {
		console.info("Initializing Player")
		var iframeElement = document.querySelector('iframe');
		var player = new PeerTubePlayer(iframeElement);
		await player.ready;
		console.info("Player ready");
		player.addEventListener("playbackStatusUpdate", checkPlaybackStatus);
	}

	playVideo(uuid, autoplay, true);
	initializePlayer();
	</script>



