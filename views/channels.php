<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<script>

		jQuery(document).ready(function(){

			jQuery('#playlists_pt .remove').click(function(){
				var ch = jQuery(this).parent('form').parent('.playlist_pt');
				jQuery.post(ajaxurl, {action: 'remove_channel_peertube', id: jQuery(this).attr('rel'), _ajax_nonce: '<?= wp_create_nonce( "remove_channel_peertube" ); ?>' }, function(){
					jQuery(ch).remove();
				});
			});

		});

</script>

<h2>All Peertube Channels</h2>
<form action="" method="post" id="form_new_pl_pt">
	<?php wp_nonce_field( 'new_ch_peertube' ) ?>
	<b>Add a new channel</b><br />
	<label>Channel ID: </label><input type="text" name="channel_id" /> You can find it in the URL of the channel: https://fair.tube/c/<strong style="color: #00fc00">channelname</strong>/videos<br />
	<label for="playlist_peertube_sd">Show Title:</label><br />
	<input type="checkbox" name="show_title" value="1" id="playlist_peertube_sd" checked /><br />
	<label for="playlist_peertube_sd">Show Description:</label><br />
	<input type="checkbox" name="show_description" value="1" id="playlist_peertube_sd" checked /><br />
	<label for="playlist_peertube_sd">Show Picture:</label><br />
	<input type="checkbox" name="show_picture" value="1" id="playlist_peertube_sd" checked /><br />
	<label for="playlist_peertube_sd">Show Videos:</label><br />
	<input type="checkbox" name="show_videos" value="1" id="playlist_peertube_sd" checked /><br />
	<label for="playlist_peertube_sd">Show Playlists:</label><br />
	<input type="checkbox" name="show_playlists" value="1" id="playlist_peertube_sd" checked /><br />
	<input type="submit" value="Add" />
</form>

<h2>Already existing Peertube Channels</h2>
<div id="playlists_pt">
<?php

if(sizeof($channels) > 0)
{
	foreach($channels as $channel)
	{
		echo '<div class="playlist_pt">';
		echo '<h3>'.$channel->channel_id.'</h3></br>';
		echo '<form action="" method="post">';
		echo wp_nonce_field( 'update_ch_peertube_'.$channel->id, "_wpnonce", true, false );
		echo '<label>Channel ID : </label>';
		echo '<input type="text" name="channel_id" value="'.$channel->channel_id.'" /><br />';
		echo '<input type="hidden" name="id" value="'.$channel->id.'" /><br />';
		echo '<label for="playlist_peertube_sd">Show Title:</label><br />';
		echo '<input type="checkbox" name="show_title" value="1" id="playlist_peertube_sd" checked /><br />';
		echo '<label for="playlist_peertube_sd">Show Description:</label><br />';
		echo '<input type="checkbox" name="show_description" value="1" id="playlist_peertube_sd" checked /><br />';
		echo '<label for="playlist_peertube_sd">Show Picture:</label><br />';
		echo '<input type="checkbox" name="show_pictture value="1" id="playlist_peertube_sd" checked /><br />';
		echo '<input type="image" src="'.plugins_url( 'embed-peertube-wp/images/save.png').'" title"Save" /> <img title="Remove this channel" class="remove action" rel="'.$channel->id.'" src="'.plugins_url( 'embed-peertube-wp/images/remove.png' ).'" />
		Shortcode : <input type="text" value="[channel_peertube id='.$channel->id.']" onClick="this.select();" />
		</form></div>';
	}
}
else
	echo 'No channel found!';
?>

</div>


