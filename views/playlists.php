
<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<script>

		jQuery(document).ready(function(){

			jQuery('#playlists_pt .remove').click(function(){
				var pl = jQuery(this).parent('form').parent('.playlist_pt');
				jQuery.post(ajaxurl, {action: 'remove_playlist_peertube', id: jQuery(this).attr('rel'), _ajax_nonce: '<?= wp_create_nonce( "remove_playlist_peertube" ); ?>' }, function(){
					jQuery(pl).remove();
				});
			});

		});

</script>

<h2>All Peertube Playlists</h2>
<form action="" method="post" id="form_new_pl_pt">
<?php wp_nonce_field( 'new_pl_peertube' ) ?>
<b>Add a new playlist</b><br />
	<label>Name: </label><input type="text" name="name" /><br />
	<label>Playlist ID: </label><input type="text" name="playlist_id" /> You find it in the URL of the playlist: https://fair.tube/w/p/<strong style="color: #00fc00">kwnt5xET6s1kijp6fUwHLh</strong><br />
	<input type="hidden" name="template" value="1">
	<label>Title video size: </label><input type="text" name="text_size" />px<br />
	<label>Title video color: </label><input type="text" name="text_color" /><br />
	<label for="playlist_peertube_sd">Show description:</label><input type="checkbox" name="show_description" value="1" id="playlist_peertube_sd" checked /><br />
	<input type="submit" value="Add" />
</form>

<div id="playlists_pt">
<?php

if(sizeof($playlists) > 0)
{
	foreach($playlists as $playlist)
	{
		echo '<div class="playlist_pt"><h3>'.$playlist->name.'</h3>';
		echo '<form action="" method="post">';
		echo wp_nonce_field( 'update_pl_peertube_'.$playlist->id, "_wpnonce", true, false );
		echo '<input type="hidden" name="id" value="'.$playlist->id.'" />';
		echo '<label>Name : </label><input type="text" name="name" value="'.$playlist->name.'" /><input type="hidden" name="id" value="'.$playlist->id.'" /><br />';
		echo '<label>Playlist ID : </label>';
		echo '<input type="text" name="playlist_id" value="'.$playlist->playlist_id.'" /><br />';
		echo '<input type="hidden" name="template" value="1">';
		echo '<label>Title video size : </label><input type="text" name="text_size" value="'.$playlist->text_size.'" />px<br />
		<label>Title video color : </label><input type="color" name="text_color" value="'.$playlist->text_color.'" /><br />';
		echo '<label for="playlist_peertube_sd">Show description:</label><input type="checkbox" name="show_description" value="1" id="playlist_peertube_sd" '.($playlist->show_description == 1 ? 'checked' : '').' /><br />';
	echo '<input type="image" src="'.plugins_url( 'embed-peertube-playlist/images/save.png').'" title"Save" /> <img title="Remove this playlist" class="remove action" rel="'.$playlist->id.'" src="'.plugins_url( 'embed-peertube-playlist/images/remove.png' ).'" />
	Shortcode : <input type="text" value="[playlist_peertube id='.$playlist->id.']" onClick="this.select();" />
	</form></div>';
	}
}
else
	echo 'No playlist found!';

?>

</div>