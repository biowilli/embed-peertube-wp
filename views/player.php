<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php
	require_once('peertubeAPI.php'); // Include the peertubeAPI.php file
	$playlistId = $_GET['playlistId'];

	$peertube_url = get_option("pl_peertube_url");

	$data = getPlaylist($playlistId); // Call the function to retrieve the playlist data

	if (is_string($data)) {
		echo $data; // Error occurred, display error message
	} 

	//var_dump($data); 
?> 

<div class="control_view">
	<div class="video_view">
		<div class="video_container" id="video_container">
			
		</div>
		<div class="description_view" id="description_view">
			<p id="description_container"> </p>
		</div>
	</div>

	<div class="playlist_view" id="playlist_view">
		<ul class="playlist_container" id="playlist_container">
		</ul>
	</div>
<div>

<script src="https://unpkg.com/@peertube/embed-api/build/player.min.js"></script>
<script>

	var clickEvent = new MouseEvent('click', {
		view: window,
		bubbles: true,
		cancelable: true
	});

	var videoContainer = document.getElementById('video_container');
	videoContainer.dispatchEvent(clickEvent);

<?php
	$peertube_playlist = "var peertube_playlist=["; // Variable to store the Peertube playlist
	$peertube_count = 0;
	foreach($data->data as $video)
	{
		if ($peertube_count != 0) {
			$peertube_playlist .= ",";
		}

		$peertube_playlist .= "{";
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
	$logo_svg = plugins_url('embed-peertube-wp/images/logo_black.svg');
	$selectedOption = get_option('pl_playbutton_style_playlist');

	switch ($selectedOption) {
		case 'playbutton_black_playlist':
			$logo_svg = plugins_url( 'embed-peertube-wp/images/playbutton_black.svg');
			break;
	
		case 'playbutton_white_playlist':
			$logo_svg =  plugins_url( 'embed-peertube-wp/images/playbutton_white.svg');
			break;
	
		case 'playbutton_fs1_playlist':
			$logo_svg =  plugins_url( 'embed-peertube-wp/images/playbutton_fs1.svg');
			break;
	
		case 'playbutton_fs1_2_playlist':
			$logo_svg =  plugins_url( 'embed-peertube-wp/images/playbutton_fs1_2.svg');
			break;
	
		default:
			$logo_svg =  plugins_url( 'embed-peertube-wp/images/playbutton_white.svg');
				break;
		}

?>
	var logoSvg = "<?php echo $logo_svg; ?>";

	var playlistContainer = document.getElementById('playlist_container');
	var currentlyPlayingVideo = -1;

	peertube_playlist.forEach(function(video, index) {
		var li = document.createElement('li');
		li.classList.add('playlist_element');

		var div = document.createElement('div');
		div.classList.add('playlist_item'); 
		
		var playbutton = document.createElement('img');
		playbutton.classList.add('play_button_playlist');
		playbutton.src = logoSvg;
		playbutton.style.width = '20px';
		playbutton.style.height = 'auto';
		playbutton.style.visibility = 'hidden'; 

		var previewImg = document.createElement('img');
		previewImg.src = video.preview;
		previewImg.style.width = '100px';
		previewImg.style.height = 'auto';

		var titleSpan = document.createElement('span');
		titleSpan.textContent = video.title;

		div.appendChild(playbutton);
		div.appendChild(previewImg);
		div.appendChild(titleSpan);

		li.appendChild(div);
		playlistContainer.appendChild(li);

		div.addEventListener('click', function(event) {
				event.preventDefault(); // Prevent the default behavior of the link
				playVideo(video.embed, video.description, index); // Call the playVideo function to play the selected video
			});
	});

	function updatePlayButtonVisibility(selectedIndex) {
		var playlistItems = document.getElementsByClassName('playlist_item');
		for (var i = 0; i < playlistItems.length; i++) {
			var playButton = playlistItems[i].querySelector('.play_button_playlist');
			if (i === selectedIndex) {
			playButton.style.visibility = 'visible';
			} else {
			playButton.style.visibility = 'hidden';
			}
		}
	}

	// Funktion, um ein Video abzuspielen
	function playVideo(embedVideoUrl, description, index) {
        // Setze die Video-URL als src-Attribut des <iframe>-Elements 350px
        var iframe = '<iframe width="100%" height="480px" src="'+ embedVideoUrl +'?autoplay=1&rel=0&peertubeLink=0&api=1" frameborder="0" allowfullscreen></iframe>';
		jQuery('#video_container').html(iframe);
		updatePlayButtonVisibility(index);
		updateDescription(description);
		initializePlayer(index);
    }


	function updateDescription(description) {
		var discription = document.getElementById('description_container');
		discription.textContent = description;
    }

	// PeerTube >= 2.2.
	function checkPlaybackStatus(status) {
		console.log("currentlyPlayingVideo"); // wird nicht richtig geupdated 
		if (status.playbackState == "ended") {
			console.log(currentlyPlayingVideo);
			if (currentlyPlayingVideo != -1) {
				var nextIndex = (currentlyPlayingVideo + 1) % peertube_playlist.length;
				var nextVideo = peertube_playlist[nextIndex];
				playVideo(nextVideo.embed, nextVideo.description, nextIndex);
			}
		}
	}

	const PeerTubePlayer = window['PeerTubePlayer']
	async function initializePlayer() {
		var iframeElement = document.querySelector('iframe');
		var player = new PeerTubePlayer(iframeElement);
		await player.ready;
		player.addEventListener("playbackStatusUpdate", checkPlaybackStatus); // Überwache den Status der Wiedergabe
		}

    jQuery(document).ready(function(){
		var targetUuid = "<?php echo $_GET['uuid']; ?>";

		var targetEmbed;
		var targetIndex;

		peertube_playlist.forEach(function(video, index) {
		
			if (video.uuid === targetUuid) {
				targetEmbed = video.embed;
				targetDescription = video.description;
				targetIndex = index;
				return; 
			}
		});

		if(targetEmbed == undefined || targetIndex == undefined){
			return;
		}

		playVideo(targetEmbed, targetDescription, targetIndex); // Rufe die Funktion playVideo auf, um das ausgewählte Video abzuspielen
    });

</script>
 