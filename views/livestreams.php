<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<script>

		jQuery(document).ready(function(){

			jQuery('#playlists_pt .remove').click(function(){
				var lt = jQuery(this).parent('form').parent('.playlist_pt');
				jQuery.post(ajaxurl, {action: 'remove_livestream_peertube', id: jQuery(this).attr('rel'), _ajax_nonce: '<?= wp_create_nonce( "remove_livestream_peertube" ); ?>' }, function(){
					jQuery(lt).remove();
				});
			});

		});

</script>

<h2>All Livestreams</h2>
<form action="" method="post" id="form_new_pl_pt">
	<?php wp_nonce_field( 'new_livestream_peertube' ) ?>
	<b>Add a new Livestream</b><br />
	<label>Livestream ID: </label><input type="text" name="livestream_id" /> You can find it in the URL of the Livestream URL: https://fair.tube/w/<strong style="color: #00fc00">livestreamid</strong><br />
	<input type="submit" value="Add" />
</form>

<h2>Already existing Peertube Livestreams</h2>
<div id="playlists_pt">
<?php

if(sizeof($livestreams) > 0)
{
	foreach($livestreams as $livestream)
	{
		echo '<div class="playlist_pt">';
		echo '<h3>'.$livestream->livestream_id.'</h3></br>';
		echo '<form action="" method="post">';
		echo wp_nonce_field( 'update_ch_peertube_'.$livestream->id, "_wpnonce", true, false );
		echo '<label>livestream ID : </label>';
		echo '<input type="text" name="livestream_id" value="'.$livestream->livestream_id.'" /><br />';
		echo '<input type="hidden" name="id" value="'.$livestream->id.'" /><br />';
		echo '<input type="image" src="'.plugins_url( 'embed-peertube-wp/images/save.png').'" title"Save" /> <img title="Remove this Livestream" class="remove action" rel="'.$livestream->id.'" src="'.plugins_url( 'embed-peertube-wp/images/remove.png' ).'" />
		Shortcode : <input type="text" value="[livestream_peertube id='.$livestream->id.']" onClick="this.select();" />
		</form></div>';
	}
}
else
	echo 'No Livestream found!';
?>

</div>


