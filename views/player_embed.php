<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php

$playlistId = $_GET['playlistId'];

$peertube_url = get_option("pl_peertube_url");
if (is_numeric($playlistId)) {
	global $wpdb;
	$playlist_peertube_table = $wpdb->prefix . "playlists_peertube";
	$query = $wpdb->prepare(
		"SELECT * FROM {$playlist_peertube_table} WHERE id = %d",
		$playlistId
	);
	$playlist = $wpdb->get_row($query);
	$embedPlugin = $peertube_url . "/video-playlists/embed/" . $playlist->playlist_id;
} else {
	echo "Missing playlist ID!";
}
?>

<div class="video">
	<div class="video_container" id="video_container">
		<iframe width="100%" height="350px" sandbox="allow-same-origin allow-scripts allow-popups" src="<?php echo $embedPlugin . "?autoplay=1&rel=0&peertubeLink=0&api=1"?>"  frameborder="0" allowfullscreen></iframe>
	</div>
</div>

<script src="https://unpkg.com/@peertube/embed-api/build/player.min.js"></script>
<script>
async function initializePlayer() {
		var iframeElement = document.querySelector('iframe');
		var player = new PeerTubePlayer(iframeElement);
		console.log(player);
		await player.ready;
		console.log(player);
		}
		//TODO: Rufe die Video-ID des aktuellen Videos ab //um kommentieren und liken zu können

		initializePlayer()
</script>


