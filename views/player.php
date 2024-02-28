<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php
	require_once('peertubeAPI.php'); // Include the peertubeAPI.php file

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
			<p id='description_container' style='color:{$description_textcolor};'>
			
			<p id='metadata_container' style='color:{$description_textcolor};'></p>
			</p>

			<hr class='line' />
			<button id='read_more_button' onclick='toggleMetadata()' style='color:#fff; background: #4fbdc8; text-shadow: none; border: none; border-radius: 5px; padding: 10px 20px; cursor: pointer; font-size: 0.8rem;'>Mehr anzeigen</button>
		</div>";
	}

	$controlView = "
	<div class='control_view' id='control_view'>
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
		<div class="blx__grid__container12" style="column-gap:<?php echo $gridgap ?>px; row-gap:<?php echo $gridgap ?>px; max-columns:<?php echo $max_column ?>">
		
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
	echo "<div class='gridContainer'>";
	foreach($data->data as $video)
	{
		if (is_null($video) || is_null($video->video) || is_null($video->video->name)){
			continue;
		}

		$gridVideoOnClick = "";
		if ($playlist->click === VIDEOLOCATION::SAMEPAGE || $show_player) {
			$gridVideoOnClick .= " onClick='playVideo(\"{$video->video->uuid}\", \"{$autoplay}\")'";
		} elseif ($playlist->click === VIDEOLOCATION::SEPARATE && !$show_player) {
			$gridVideoOnClick .= " onClick='playVideoInSeparatePage(\"{$video->video->uuid}\")'";
		}

		$videoElementHover = "
		<div class='grid_item_2' {$gridVideoOnClick} style='display: none; background:{$grid_backgroundcolor};'>
			<div class='outer_container_2'>
				<div class='tile__container_2' style='background:{$grid_backgroundcolor};'>
					<div class='tile__wrapper_2'>
						<div class='tile__content_2' style='background:{$grid_backgroundcolor}; transition-delay:${pl_hover_delay}ms;'>
							<div class='media__wrapper_2'>
								<div class='media__content_2'>
									<div class='thumbnail_container_2'>
										<img class='thumbnail_2' height='100%' width='100%' src='{$peertube_url}{$video->video->previewPath}' />
										{$playbuttonElement}
									</div>
								</div>
							</div>

							<div class='blx__title__content_2'>
								<div class='text_content' style='color:{$grid_textcolor}; font-size:{$grid_textsize_header}px;'>{$video->video->name}</div>
							</div>
							<div class='text_content_2' style='color:{$grid_textcolor}; font-size:{$grid_textsize_description}px; background:{$grid_backgroundcolor}; transition-delay:${pl_hover_delay}ms;'>
							{$video->video->description}
						</div>
						</div>
					</div>
				</div>
			</div>
		</div>";

		$videoElement2 = "
		<div class='grid_item' {$gridVideoOnClick}>
			<div class='outer_container'>
				<div class='tile__container' style='background:{$grid_backgroundcolor};'>
					<div class='tile__wrapper'>
						<div class='tile__content' style='background:{$grid_backgroundcolor}; transition-delay:${pl_hover_delay}ms;'>
							<div class='media__wrapper'>
								<div class='media__content'>
									<div class='thumbnail_container'>
										<img class='thumbnail' height='100%' width='100%' src='{$peertube_url}{$video->video->previewPath}' />
										{$playbuttonElement}
									</div>
								</div>
							</div>

							<div class='blx__title__content clamp'>
								<div class='text_content' style='color:{$grid_textcolor}; font-size:11px;'>{$video->video->name}</div>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>";

		echo $videoElement2;
		echo $videoElementHover;
	}
	echo "<div>";
}
?>
</div>
</div> 
<script>

// TODO: Transistion BUG

const gridItems = document.querySelectorAll('.grid_item');
const gridItem2s = document.querySelectorAll('.grid_item_2');
var transitioningOfMetadata = false;
gridItems.forEach((gridItem, index) => {
    const gridItem2 = gridItem2s[index]; 
    let isGridItemHovered = false;
    let isGridItem2Hovered = false;
    let isLeavingGridItem2 = false;

    gridItem.addEventListener('mouseenter', function() {
		if (transitioningOfMetadata) {
			return; 
		}


        isGridItemHovered = true;
        const rect = gridItem.getBoundingClientRect();
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
        gridItem2.style.display = 'block';
        gridItem2.style.width = `${rect.width}px`;
        gridItem2.style.height = `${rect.height}px`;
        gridItem2.style.top = `${rect.top + scrollTop}px`;
        gridItem2.style.left = `${rect.left + scrollLeft}px`;
        setTimeout(() => {
            gridItem2.style.opacity = '1';
            gridItem2.style.transform = 'scale(1.1)';
        }, 10);
    });

    gridItem.addEventListener('mouseleave', function() {
        isGridItemHovered = false;
        hideGridItem2IfNotHovered();
    });

    gridItem2.addEventListener('mouseenter', function() {
        isGridItem2Hovered = true;
        isLeavingGridItem2 = false;
        gridItem2.style.opacity = '1';
    });

    gridItem2.addEventListener('mouseleave', function() {
        isGridItem2Hovered = false;
        isLeavingGridItem2 = true;
        hideGridItem2IfNotHovered();
    });

    function hideGridItem2IfNotHovered() {
        setTimeout(() => {
            if ((!isGridItemHovered && !isGridItem2Hovered) || isLeavingGridItem2) {
                gridItem2.style.opacity = '0';
                gridItem2.style.transform = 'scale(1)';
                setTimeout(() => {
                    gridItem2.style.display = 'none';
                }, 10);
            }
        }, 10);
    }
});
	window.blxMasonryStore = new Map([
    ['resize_lock', false],
    ['resize_value', { width: 0, height: 0 }],
    ['elements', []],
    ['elements_map', new Map()],
    ['tiles', []],
    ['current_tile', null],
    ['z_counter', 1],
    ['z_counter_resetTimeout', null],
    ['re_removeLastWord', new RegExp('[\s\n\r]+[^\s\n\r]+[\s\n\r]*$', 'i')]
]);

// Reset z_counter on all tiles except the current one
window.blxMasonryStore.set('resetZCounter', () => {
    window.blxMasonryStore.set('z_counter', 1);
    const z_counter = window.blxMasonryStore.get('z_counter');
    const current_tile = window.blxMasonryStore.get('current_tile');
    for (const tile of window.blxMasonryStore.get('tiles')) {
        if (tile.element !== current_tile) {
            tile.z_counter = z_counter;
            tile.element.setAttribute('style', '--_z:' + z_counter + ';');
        }
    }
});

// Ellipsify text
window.blxMasonryStore.set('ellipsify', () => {
    for (const obj of window.blxMasonryStore.get('elements')) {
        let content = (' ' + obj.content).trim();
        obj.child.innerHTML = content;

        const elemRect = obj.element.getClientRects()[0];
        const elemWidth = elemRect.width;
        const elemHeight = elemRect.height;

        let rect = obj.child.getClientRects()[0];
        while (rect.height > elemHeight) {
            let idx = content.lastIndexOf(' ')
            if (idx === -1) {
                break;
            }
            content = content.slice(0, idx).trim();
            obj.child.innerHTML = content + '...';
            rect = obj.child.getClientRects()[0];
        }
        while (rect.width > elemWidth) {
            let t_content = content.slice(0, -5);
            if (t_content.length < 5) {
                content = content.slice(0, 5);
                obj.child.innerHTML = content + '...';
                break;
            }
            content = t_content;
            obj.child.innerHTML = content + '...';
            rect = obj.child.getClientRects()[0];
        }
    }
});

// Throttle resize events
window.blxMasonryStore.set('throttleResizeEvents', (evtType) => {
    if (!window.blxMasonryStore.get('resize_lock')) {
        window.blxMasonryStore.set('resize_lock', true);
        window.requestAnimationFrame(() => {
            // Ellipsify text
            window.blxMasonryStore.get('ellipsify')();

            // Dispatch throttled resize event
            window.dispatchEvent(new CustomEvent('blx-resize', {
                detail: {
                    size: window.blxMasonryStore.get('resize_value')
                }
            }));
            window.blxMasonryStore.set('resize_lock', false);
        });
    }
});


// Get all tile elements
// Add event listeners for mouseenter and mouseleave
for (const element of document.getElementsByClassName('blx__tile__outer_container')) {
    const f_enter = () => {
        window.blxMasonryStore.set('current_tile', element);
    };
    const f_leave = () => {
        window.blxMasonryStore.set('current_tile', null);
        const z_counter = window.blxMasonryStore.get('z_counter') + 1;
        window.blxMasonryStore.set('z_counter', z_counter);
        for (const tile of window.blxMasonryStore.get('tiles')) {
            if (tile.element !== element) {
                tile.z_counter = z_counter;
                tile.element.setAttribute('style', '--_z:' + z_counter + ';');
            }
        }
        clearTimeout(window.blxMasonryStore.get('z_counter_resetTimeout'));
        window.blxMasonryStore.set('current_tile', setTimeout(
            window.blxMasonryStore.get('resetZCounter'),
            2000
        ));
    };
    window.blxMasonryStore.get('tiles').push({
        element: element,
        z_counter: 1,
        f_enter: f_enter,
        f_leave: f_leave
    });
    element.addEventListener('mouseenter', f_enter);
    element.addEventListener('mouseleave', f_leave);
}

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
    window.blxMasonryStore.get('elements').push(obj);
    window.blxMasonryStore.get('elements_map').set(element, obj);
}

// Add event listener for resize event
window.addEventListener('resize', (evt) => {
    window.blxMasonryStore.set('resize_value', {
        width: window.innerWidth,
        height: window.innerHeight
    });
    window.blxMasonryStore.get('throttleResizeEvents')('resize');
});

// Initial ellipsifying of text
window.blxMasonryStore.get('ellipsify')();

</script>
<script src="https://unpkg.com/@peertube/embed-api/build/player.min.js"></script>
<script>
<?php
	echo "var peertubeUrl = '" .$peertube_url . "';"; 
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
    //echo "console.log('videouuid:', uuid);";
    echo "var autoplay = " . get_option("pl_autoplay") . ";";
    //echo "console.log('general autoplay from settings:', autoplay);";
    echo "var initalAutoplay = " . $playlist->autoplay_video . ";";
    //echo "console.log('autoplay_video inital:', initalAutoplay);";
	echo "var scrollToVideo = " . $playlist->scroll_video . ";";
    //echo "console.log('scrollToVideo inital:', scrollToVideo);";
	echo "var seperatePage = " . $playlist->click . ";";
    //echo "console.log('seperate page 1=:', seperatePage);";
?>
	console.log(peertubeUrl);
	var currentlyPlayingVideo = 0;

	function playVideoInSeparatePage(uuid) {
		var siteUrl = '<?php echo site_url(); ?>';
		window.location.href = siteUrl + '/index.php/playlist/?uuid=' + encodeURIComponent(uuid) + '&playlistId=' + encodeURIComponent('<?php echo $playlist->id?>');
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
/*
		if (seperatePage) {
			var currentUrl = new URL(window.location.href);
				currentUrl.searchParams.set('uuid', uuid);
				history.pushState({}, '', currentUrl.toString());
		}*/

		if (scrollToVideo && !inital) {
			var conrtolView = document.getElementById('control_view');
			if (conrtolView) {
				var bodyRect = document.body.getBoundingClientRect();
				var elemRect = conrtolView.getBoundingClientRect();
				offset   = elemRect.top - bodyRect.top - 80;
				window.scrollTo({
					top: offset,
					behavior: 'smooth'
				});
			}
		}

		jQuery('#video_container_iframe').html(iframe);

		updateStyle(targetIndex);
		updateDescription(targetDescription);
		updateMetadata(targetUuid);
	}

	function toggleMetadata(close) {
		transitioningOfMetadata = true;

		var container = document.getElementById("description_container");
		container.addEventListener("transitionend", function() {
					transitioningOfMetadata = false;
				});
		var button = document.getElementById("read_more_button");
		if (close) {
			container.style.maxHeight = null;
			button.innerHTML = "Mehr anzeigen";
		} else {
			if (container.style.maxHeight) {
				container.style.maxHeight = null;
				button.innerHTML = "Mehr anzeigen";
			} else {
				container.style.maxHeight = container.scrollHeight + "px";
				button.innerHTML = "Weniger anzeigen";
			}
		}
	}

	function updateDescription(targetDescription) {
		var videoDescriptionContainer = document.getElementById('description_container');
		if (videoDescriptionContainer && targetDescription) {
			videoDescriptionContainer.textContent = targetDescription;
		}
	}

	async function fetchCreatorRoleInfo(id) {
		const response = await fetch(`${peertubeUrl}/plugins/metadata/1.5.3/router/creator/${id}`);
		const data = await response.json();
		return data;
	}
	async function fetchOrganizationRoleInfo(id) {
		const response = await fetch(`${peertubeUrl}/plugins/metadata/1.5.3/router/organization/${id}`);
		const data = await response.json();
		return data;
	}

	function formatDate(dateString) {
		var date = new Date(dateString);
		var day = date.getDate();
		var month = date.getMonth() + 1; 
		var year = date.getFullYear();

		if (day < 10) {
			day = '0' + day;
		}
		if (month < 10) {
			month = '0' + month;
		}

		return day + '.' + month + '.' + year;
	}

	function secToTime(duration) {
		var seconds = Math.floor(duration % 60),
			minutes = Math.floor((duration / 60) % 60),
			hours = Math.floor((duration / (60 * 60)) % 24),
			days = Math.floor(duration / (60 * 60 * 24));
			
			var result = "";
			if (days > 0) {
				result += days + " Tage, ";
			}
			if (hours > 0) {
				result += hours + " Stunden, ";
			}
			if (minutes > 0) {
				result += minutes + " Minuten, ";
			}
			result += seconds + " Sekunden";

			return result;
	}

	function updateMetadata(targetUuid) {
	toggleMetadata(close)
    var metadataContainer = document.getElementById('description_container');
    metadataContainer.innerHTML = ''; 
	//peertubeUrl = "http://localhost:9000"
    fetch(`${peertubeUrl}/plugins/metadata/1.5.3/router/metadata/${targetUuid}`)
    .then(response => {
        if (!response.ok) {
			console.log("fetch not successfull");
            throw new Error('Network response was not ok');
        }
		console.log("fetch was successfull");
        return response.json();
    })
    .then(async data => {
        console.log(data);
        var metadataContainer = document.getElementById('description_container');
        if (metadataContainer && data) {
            console.info("metadata from Peertube with UUID:", targetUuid);

            var table = document.createElement('table');
            var tbody = document.createElement('tbody');
            var fields = {
                'show.title.title': 'Titel',
				'show.description.text': 'Beschreibung',
				'mediainfo.info.duration': 'Dauer',
				'show.season': 'Staffel',
				'show.episode': 'Folge',
				'show.category': 'Kategorie',
				'show.productionDate': 'Produktionsdatum',
				'show.description.oldtags': 'Tags',
				'show.role.presenter': 'Moderator',
				'show.role.guests': 'Gäste',
				"show.role.crew": 'Crew',
				"show.role.producer": 'Produzent'
            };

            for (const [key, label] of Object.entries(fields)) {
				console.log(label);
                if (data[key]) {
					console.log(label);
					console.log(data[key]);
                    var row = document.createElement('tr');
                    var keyCell = document.createElement('td');
                    var valueCell = document.createElement('td');

                    keyCell.textContent = label;
					var formattedValue = data[key];
					if (key === 'show.category') {
						var categoryIds =[
							{"key":19,"label":"Animation&Experimente"},
							{"key":20,"label":"Doku"},
							{"key":21,"label":"Events&Festivals"},
							{"key":22,"label":"FUELLER"},
							{"key":23,"label":"Info&Service"},
							{"key":24,"label":"Jugend"},
							{"key":25,"label":"Kunst&Kultur"},
							{"key":26,"label":"Medienwerkstatt"},
							{"key":27,"label":"Musik"},
							{"key":28,"label":"Politik&Gesellschaft"},
							{"key":29,"label":"Spielfilm&Serien"},
							{"key":30,"label":"Sport"},
							{"key":31,"label":"Trailer"},
							{"key":32,"label":"TRENNER"},
							{"key":33,"label":"Unterhaltung"},
							{"key":34,"label":"Programmtrailer"},
							{"key":35,"label":"PTR"}];


						var categoryLabel = "";
						for (var i = 0; i < categoryIds.length; i++) {
							if (categoryIds[i].key === data[key]) {
								categoryLabel = categoryIds[i].label;
								break;
							}
						}
						formattedValue = categoryLabel;

					} else if (key === 'show.description.oldtags') {
						formattedValue = data[key].join(', ');
					} else if (key.startsWith('show.role')) {
						if (key === 'show.role.presenter') {
							var ids = data[key].split(',').map(id => id.trim());
							var roleInfos = await Promise.all(ids.map(fetchCreatorRoleInfo)); 
							formattedValue = roleInfos.map(info => info[0].name).join(', '); 
						} else if (key === 'show.role.guests') {
							var ids = data[key].split(',').map(id => id.trim());
							var roleInfos = await Promise.all(ids.map(fetchCreatorRoleInfo)); 
							formattedValue = roleInfos.map(info => info[0].name).join(', '); 
						} else if (key === 'show.role.crew') {
							var ids = data[key].split(',').map(id => id.trim());
							var roleInfos = await Promise.all(ids.map(fetchCreatorRoleInfo)); 
							formattedValue = roleInfos.map(info => info[0].name).join(', '); 
						} else if (key === 'show.role.producer') {
							var ids = data[key].split(',').map(id => id.trim());
							var roleInfos = await Promise.all(ids.map(fetchOrganizationRoleInfo)); 

							formattedValue = roleInfos.map(info => info[0].name).join(', '); 
						} 
					} else if (key === 'mediainfo.info.duration'){
						formattedValue = secToTime(data[key]);
					} else if (key === 'show.productionDate'){
						formattedValue = formatDate(data[key]);
					}

					valueCell.textContent = formattedValue;

                    row.appendChild(keyCell);
                    row.appendChild(valueCell);
					console.log(row);
                    tbody.appendChild(row);

                }
            }

            table.appendChild(tbody);
            metadataContainer.appendChild(table);
			console.log(table)
        }
    })
    .catch(error => {
        console.error('There was a problem with the request:', error);
    });
}


//TODO Description margin
//TODO Fomrat for website

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
		var gridItems = document.querySelectorAll('.grid_item');
		console.log(gridItems);
		console.log(selectedIndex)
		gridItems.forEach(function(gridItem, index) {
			if (index == selectedIndex) {
				gridItem.style.boxShadow = '0px 0px 5px 5px #4cbdc9';
				console.log(gridItem);
			} else {
				gridItem.style.boxShadow = 'none';
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

	initializePlayer();
	playVideo(uuid, autoplay, true);
	</script>



