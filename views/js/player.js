var currentlyPlayingVideo = 0;

async function playVideo(uuid, autoplay, inital = false) {
  if (window.peertubePlaylistData.currentlyPlaying == uuid) {
    return;
  }

  var targetEmbed;
  var targetIndex;
  var targetUuid;

  window.peertubePlaylistData.peertubePlaylist.forEach(function (video) {
    if (video.uuid === uuid) {
      targetUuid = video.uuid;
      targetEmbed = video.embed;
      targetDescription = video.description;
      targetIndex = video.index;
      return;
    }
  });

  if (
    targetEmbed == undefined ||
    targetIndex == undefined ||
    targetUuid == undefined
  ) {
    return;
  }

  window.peertubePlaylistData.currentlyPlaying = uuid;
  currentlyPlayingVideo = targetIndex;
  var src =
    targetEmbed + "?autoplay=" + autoplay + "&rel=0&peertubeLink=0&api=1";
  var iframeElement = await initializePlayer(src);
  if (window.peertubePlaylistData.scrollToVideo && !inital) {
    var control_view = document.querySelector(".control_view");
    console.log("control_view", control_view);
    if (control_view) {
      var bodyRect = document.body.getBoundingClientRect();
      var elemRect = control_view.getBoundingClientRect();
      var offset = elemRect.top - bodyRect.top - 80;
      window.scrollTo({
        top: offset,
        behavior: "smooth",
      });
    }
  }

  updateStyle(targetIndex);
  updateDescription(targetDescription);
  updateMetadata(targetUuid);
}

function toggleMetadata(close) {
  if (!close) {
    window.transitioningOfMetadata = true;
  }

  var container = document.getElementById("description_container");
  console.log("description_container", description_container);
  container.addEventListener("transitionend", function () {
    window.transitioningOfMetadata = false;
  });
  var button = document.getElementById("read_more_button");
  console.log("button", button);
  const firstTdHeight = document.querySelector(
    "#description_container tr:first-child"
  ).offsetHeight;
  console.log("firstTdHeight", firstTdHeight);
  const descriptionContainer = container.querySelector(
    "#description_container tr:nth-child(2)"
  );
  console.log("descriptionContainer", descriptionContainer);
  const textContainer = container.querySelector(
    "#description_container tr:nth-child(2) td"
  );
  console.log("textContainer", textContainer);

  const secondTdHeight = descriptionContainer.offsetHeight;

  const lineHeight = parseFloat(
    window.getComputedStyle(descriptionContainer).lineHeight
  );
  const maxLines = 3;
  const maxHeightLines = Math.min(secondTdHeight, lineHeight * maxLines);
  console.log("maxHeightLines", maxHeightLines);
  console.log("close", close);
  if (maxHeightLines == 54) {
    var maxheightBoth = firstTdHeight + maxHeightLines;
  } else {
    var maxheightBoth = firstTdHeight + maxHeightLines + 15;
  }

  if (close || container.style.maxHeight == container.scrollHeight + "px") {
    descriptionContainer.style.overflow = "hidden";
    descriptionContainer.style.textOverflow = "ellipsis";
    container.style.maxHeight = maxheightBoth + "px";

    button.innerHTML = "Mehr anzeigen";
  } else {
    container.style.maxHeight = container.scrollHeight + "px";

    button.innerHTML = "Weniger anzeigen";
  }
}

function updateDescription(targetDescription) {
  var videoDescriptionContainer = document.getElementById(
    "description_container"
  );
  if (videoDescriptionContainer && targetDescription) {
    videoDescriptionContainer.textContent = targetDescription;
  }
}
//TODO: ask witch plugin version is here active
async function fetchCreatorRoleInfo(id) {
  var peertubeUrl = window.peertubePlaylistData.peertubeUrl;
  const response = await fetch(
    `${peertubeUrl}/plugins/metadata/1.8.4/router/creator/${id}`
  );
  const data = await response.json();
  return data;
}
async function fetchOrganizationRoleInfo(id) {
  var peertubeUrl = window.peertubePlaylistData.peertubeUrl;
  const response = await fetch(
    `${peertubeUrl}/plugins/metadata/1.8.4/router/organization/${id}`
  );
  const data = await response.json();
  return data;
}

function formatDate(dateString) {
  var date = new Date(dateString);
  var day = date.getDate();
  var month = date.getMonth() + 1;
  var year = date.getFullYear();

  if (day < 10) {
    day = "0" + day;
  }
  if (month < 10) {
    month = "0" + month;
  }

  return day + "." + month + "." + year;
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
  var metadataContainer = document.getElementById("description_container");
  metadataContainer.innerHTML = "";
  var peertubeUrl = window.peertubePlaylistData.peertubeUrl;
  fetch(`${peertubeUrl}/plugins/metadata/1.8.4/router/metadata/${targetUuid}`)
    .then((response) => {
      if (!response.ok) {
        console.log("fetch not successfull");
        throw new Error("Network response was not ok");
      }
      console.log("fetch was successfull");
      return response.json();
    })
    .then(async (data) => {
      console.log(data);
      var metadataContainer = document.getElementById("description_container");
      if (metadataContainer && data) {
        console.info("metadata from Peertube with UUID:", targetUuid);

        var table = document.createElement("table");
        var tbody = document.createElement("tbody");
        var fields = {
          "show.title.title": "Titel",
          "show.description.text": "Beschreibung",
          "mediainfo.info.duration": "Dauer",
          "show.season": "Staffel",
          "show.episode": "Folge",
          "show.category": "Kategorie",
          "show.productionDate": "Produktionsdatum",
          "show.description.tags": "Tags",
          "show.role.presenter": "Moderation",
          "show.role.guests": "Gäste",
          "show.role.crew": "Crew",
          "show.role.producer": "Produzent",
        };

        for (const [key, label] of Object.entries(fields)) {
          console.log(label);
          if (data[key]) {
            console.log(label);
            console.log(data[key]);
            var row = document.createElement("tr");
            var keyCell = document.createElement("td");
            var valueCell = document.createElement("td");

            if ("show.title.title" == key) {
              var keyCell = document.createElement("td");
              keyCell.textContent = data[key];
              keyCell.colSpan = 2;
              row.appendChild(keyCell);
              tbody.appendChild(row);
              continue;
            }

            if ("show.description.text" == key) {
              var keyCell = document.createElement("td");
              keyCell.textContent = data[key];
              keyCell.colSpan = 2;
              row.appendChild(keyCell);
              tbody.appendChild(row);
              continue;
            }

            keyCell.textContent = label;
            var formattedValue = data[key];
            if (key === "show.category") {
              var categoryIds = [
                { key: 19, label: "Animation&Experimente" },
                { key: 20, label: "Doku" },
                { key: 21, label: "Events&Festivals" },
                { key: 22, label: "FUELLER" },
                { key: 23, label: "Info&Service" },
                { key: 24, label: "Jugend" },
                { key: 25, label: "Kunst&Kultur" },
                { key: 26, label: "Medienwerkstatt" },
                { key: 27, label: "Musik" },
                { key: 28, label: "Politik&Gesellschaft" },
                { key: 29, label: "Spielfilm&Serien" },
                { key: 30, label: "Sport" },
                { key: 31, label: "Trailer" },
                { key: 32, label: "TRENNER" },
                { key: 33, label: "Unterhaltung" },
                { key: 34, label: "Programmtrailer" },
                { key: 35, label: "PTR" },
              ];

              var categoryLabel = "";
              for (var i = 0; i < categoryIds.length; i++) {
                if (categoryIds[i].key === data[key]) {
                  categoryLabel = categoryIds[i].label;
                  break;
                }
              }
              formattedValue = categoryLabel;
            } else if (key.startsWith("show.role")) {
              if (key === "show.role.presenter") {
                var ids = data[key].split(",").map((id) => id.trim());
                var roleInfos = await Promise.all(
                  ids.map(fetchCreatorRoleInfo)
                );

                formattedValue =
                  roleInfos && roleInfos.length > 0
                    ? roleInfos
                        .map((info) => {
                          if (!info[0]) {
                            return "";
                          }

                          const name = info[0].name || "";
                          const familyname = info[0].familyname || "";
                          return name + familyname;
                        })
                        .join(", ")
                    : "";
              } else if (key === "show.role.guests") {
                var ids = data[key].split(",").map((id) => id.trim());
                var roleInfos = await Promise.all(
                  ids.map(fetchCreatorRoleInfo)
                );

                formattedValue =
                  roleInfos && roleInfos.length > 0
                    ? roleInfos
                        .map((info) => {
                          if (!info[0]) {
                            return "";
                          }

                          const name = info[0].name || "";
                          const familyname = info[0].familyname || "";
                          return name + familyname;
                        })
                        .join(", ")
                    : "";
              } else if (key === "show.role.crew") {
                var ids = data[key].split(",").map((id) => id.trim());
                var roleInfos = await Promise.all(
                  ids.map(fetchCreatorRoleInfo)
                );

                formattedValue =
                  roleInfos && roleInfos.length > 0
                    ? roleInfos
                        .map((info) => {
                          if (!info[0]) {
                            return "";
                          }
                          console.log();
                          console.log("console", info[0]);
                          const name = info[0].name || "";
                          const familyname = info[0].familyname || "";
                          return name + familyname;
                        })
                        .join(", ")
                    : "";
              } else if (key === "show.role.producer") {
                var ids = data[key].split(",").map((id) => id.trim());
                var roleInfos = await Promise.all(
                  ids.map(fetchOrganizationRoleInfo)
                );

                formattedValue = roleInfos
                  .map((info) => {
                    if (!info[0]) {
                      return "";
                    }

                    const name = info[0].name || "";
                    const familyname = info[0].familyname || "";
                    return name + familyname;
                  })
                  .join(", ");
              }
            } else if (key === "show.productionDate") {
              formattedValue = formatDate(data[key]);
            } else if (key === "mediainfo.info.duration") {
              //formattedValue = secToTime(data[key]);
              continue;
            } else if (key === "show.description.tags") {
              //formattedValue = data[key].join(', ');
              continue;
            }

            valueCell.textContent = formattedValue;

            row.appendChild(keyCell);
            row.appendChild(valueCell);
            console.log("row", row);
            tbody.appendChild(row);
          }
        }

        table.appendChild(tbody);
        metadataContainer.appendChild(table);
        console.log(table);
      }
      toggleMetadata(true);
    })
    .catch((error) => {
      console.error("There was a problem with the request:", error);
    });
}
function checkPlaybackStatus(status) {
  if (status.playbackState == "ended") {
    if (currentlyPlayingVideo != -1) {
      var nextIndex =
        (currentlyPlayingVideo + 1) %
        window.peertubePlaylistData.peertubePlaylist.length;
      var nextVideo = window.peertubePlaylistData.peertubePlaylist[nextIndex];
      playVideo(nextVideo.uuid, autoplay);
    }
  }
}

function updateStyle(selectedIndex) {
  var gridItems = document.querySelectorAll(".grid_item");
  console.log(gridItems);
  console.log(selectedIndex);
  gridItems.forEach(function (gridItem, index) {
    if (index == selectedIndex) {
      gridItem.style.boxShadow = "0px 0px 5px 5px #4cbdc9";
      console.log(gridItem);
    } else {
      gridItem.style.boxShadow = "none";
    }
  });
}

const PeerTubePlayer = window["PeerTubePlayer"];
async function initializePlayer(src) {
  console.info("Initializing Player");
  var container = document.getElementById("video_container_iframe");
  if (container) {
    while (container.firstChild) {
      container.removeChild(container.firstChild);
    }

    var iframeElement = document.createElement("iframe");
    iframeElement.src = src;
    iframeElement.width = "100%";
    iframeElement.height = "100%";
    iframeElement.setAttribute("frameborder", "0");
    iframeElement.setAttribute("allowfullscreen", "");

    container.appendChild(iframeElement);

    var player = new PeerTubePlayer(iframeElement);
    await player.ready;
    player.addEventListener("playbackStatusUpdate", checkPlaybackStatus);

    return iframeElement;
  } else {
    console.error(
      "Container-Element mit ID 'video_container_iframe' nicht gefunden."
    );
  }
}
var firstVideoUuid = window.peertubePlaylistData.peertubePlaylist[0].uuid;
var autoplay = window.peertubePlaylistData.autoplay;

playVideo(firstVideoUuid, autoplay, true);
