<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<script>

	jQuery(document).ready(function(){
        jQuery('#playlist_peertube_grid_<?= $playlist->id ?> .video .video_container').click(function(e){
            e.preventDefault();
            // Get the video URL
            var uuid = jQuery(this).attr('rel');
            // Get the base URL of the WordPress site
            var siteUrl = '<?php echo site_url(); ?>';
            // Redirect to the separate page with the video URL as a parameter
            window.location.href = siteUrl + '/index.php/playlist/?uuid=' + encodeURIComponent(uuid) + '&playlistId=' + encodeURIComponent(<?= $playlist->id ?>) ;
        });
    });
</script>

<div class="playlist_peertube_grid" id="playlist_peertube_grid_<?= $playlist->id ?>">
<?php

//Style Grid
$pl_hover_delay = get_option('pl_hover_delay');
$pl_hover_delay = get_option('pl_autoplay');

$grid_backgroundcolor = get_option("pl_grid_backgroundcolor"); 
$grid_textcolor = get_option("pl_grid_textcolor"); 

$grid_textsize_header = get_option("pl_grid_textsize_header"); 
$grid_textsize_description = get_option("pl_grid_textsize_description"); 

$marginTopVideo = get_option("pl_grid_gap"); 

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
	echo  'transition-delay:' . $pl_hover_delay   . 'ms; ';
	echo 'margin-top: ' . $marginTopVideo . 'px; ';
	echo 'margin-right: ' . $marginRightVideo . 'px; ';
	echo 'margin-bottom: ' . $max_column . 'px; ';
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
	echo '">';
	echo '<div class="thumbnail_container">';
	echo '<img class="thumbnail" src="'.$peertube_url.$video->video->previewPath.'" />';
	$selectedOption = get_option('pl_playbutton_style_grid');

	switch ($selectedOption) {
		case 'playbutton_black_grid':
			echo '<img class="play_video" src="'.plugins_url( 'embed-peertube-wp/images/playbutton_black.svg').'" />';
			break;

		case 'playbutton_white_grid':
			echo '<img class="play_video" src="'.plugins_url( 'embed-peertube-wp/images/playbutton_white.svg').'" />';
			break;

		case 'playbutton_fs1_grid':
			echo '<img class="play_video" src="'.plugins_url( 'embed-peertube-wp/images/playbutton_fs1.svg').'" />';
			break;

		case 'playbutton_fs1_2_grid':
			echo '<img class="play_video" src="'.plugins_url( 'embed-peertube-wp/images/playbutton_fs1_2.svg').'" />';
			break;

		default:
			echo '<img class="play_video" src="'.plugins_url( 'embed-peertube-wp/images/playbutton_white.svg').'" />';
			break;
	}

	echo '</div>';
	
	if($playlist->show_description == 1){

		echo '<div class="information_container">';
		if($playlist->show_title) {
			echo '<div class="header_container">';
			echo '<h3 class="grid_header" style="color: '.$grid_textcolor.'; font-size: '.$grid_textsize_header.'px;">'.$video->video->name.'</h3>';
			echo '</div>';
		}
			echo '<div class="description_container">';

			//$descriptionLines = explode("\n", $video->video->description);
			//$visibleLines = array_slice($descriptionLines, 0, 3);
			//$visibleDescription = implode("\n", $visibleLines);

			echo '<p class="video_description" style="color: '.$grid_textcolor.'; font-size: '.$grid_textsize_description.'px;">';

			echo $video->video->description;
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

	} 
    echo '</div>'; // Close thumbnail_container_hover
    echo '</div>'; // Close video_container
    echo '</div>'; // Close video
}

?>


</div>