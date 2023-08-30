<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="channel_peertube_grid" id="channel_peertube_grid_<?= $channel->id ?>">
<?php

$avatarUrl = '';
if (isset($data->avatar)) {
    $avatarUrl = $data->avatar->path;
}

$host = $data->host;
$channelName = $data->displayName;
$channelDescription = $data->description;
$amountFollower = $data->followersCount;

echo '<div class="channel__heading">';
if (!empty($avatarUrl) || !empty($avatarAlt)) {
    echo '  <div class="channel__heading__avatar">';
    echo '    <img src="https://' . $host . $avatarUrl . '">';
    echo '  </div>';
}
echo '  <div class="channel__heading__info">';
echo '    <h1 class="channel__heading__name">' . $channelName . '</h1>';
echo '    <ul class="channel__heading__counts">';
echo '      <li><span>' . $amountFollower . ' Abonnenten</span></li>';
echo '    </ul>';
echo '  </div>';
echo '</div>';
echo '<p class="channel__heading__description">' . $channelDescription . '</p>';
?>

</div>