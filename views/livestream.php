<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="channel_peertube_grid" id="livestream<?= $livestream->id ?>">
<?php

echo '<iframe width="560" height="315" src="https://test-vod.fs1.tv/videos/embed/' . $livestream->livestream_id . '" frameborder="0" allowfullscreen="" sandbox="allow-same-origin allow-scripts allow-popups"></iframe>';
?>
</div>