
const gridItems = document.querySelectorAll(".grid_item");
const gridItem2s = document.querySelectorAll(".grid_item_2");
window.transitioningOfMetadata = false;
console.log(window.transitioningOfMetadata);
gridItems.forEach((gridItem, index) => {
  const gridItem2 = gridItem2s[index];
  let isGridItemHovered = false;
  let isGridItem2Hovered = false;
  let isLeavingGridItem2 = false;

  gridItem.addEventListener("mouseenter", function () {
    console.log("gridItems");
    console.log("transitioningOfMetadata", window.transitioningOfMetadata);
    if (window.transitioningOfMetadata) {
      return;
    }

    isGridItemHovered = true;
    const rect = gridItem.getBoundingClientRect();
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const scrollLeft =
      window.pageXOffset || document.documentElement.scrollLeft;
    gridItem2.style.display = "block";
    gridItem2.style.width = `${rect.width}px`;
    gridItem2.style.height = `${rect.height}px`;
    gridItem2.style.top = `${rect.top + scrollTop}px`;
    gridItem2.style.left = `${rect.left + scrollLeft}px`;
    setTimeout(() => {
      gridItem2.style.opacity = "1";
      gridItem2.style.transform = "scale(1.1)";
    }, 10);
  });

  gridItem.addEventListener("mouseleave", function () {
    isGridItemHovered = false;
    hideGridItem2IfNotHovered();
  });

  gridItem2.addEventListener("mouseenter", function () {
    isGridItem2Hovered = true;
    isLeavingGridItem2 = false;
    gridItem2.style.opacity = "1";
  });

  gridItem2.addEventListener("mouseleave", function () {
    isGridItem2Hovered = false;
    isLeavingGridItem2 = true;
    hideGridItem2IfNotHovered();
  });

  function hideGridItem2IfNotHovered() {
    setTimeout(() => {
      if ((!isGridItemHovered && !isGridItem2Hovered) || isLeavingGridItem2) {
        gridItem2.style.opacity = "0";
        gridItem2.style.transform = "scale(1)";
        setTimeout(() => {
          gridItem2.style.display = "none";
        }, 10);
      }
    }, 10);
  }
});
window.blxMasonryStore = new Map([
  ["resize_lock", false],
  ["resize_value", { width: 0, height: 0 }],
  ["elements", []],
  ["elements_map", new Map()],
  ["tiles", []],
  ["current_tile", null],
  ["z_counter", 1],
  ["z_counter_resetTimeout", null],
  ["re_removeLastWord", new RegExp("[s\n\r]+[^s\n\r]+[s\n\r]*$", "i")],
]);

// Reset z_counter on all tiles except the current one
window.blxMasonryStore.set("resetZCounter", () => {
  window.blxMasonryStore.set("z_counter", 1);
  const z_counter = window.blxMasonryStore.get("z_counter");
  const current_tile = window.blxMasonryStore.get("current_tile");
  for (const tile of window.blxMasonryStore.get("tiles")) {
    if (tile.element !== current_tile) {
      tile.z_counter = z_counter;
      tile.element.setAttribute("style", "--_z:" + z_counter + ";");
    }
  }
});

// Ellipsify text
window.blxMasonryStore.set("ellipsify", () => {
  for (const obj of window.blxMasonryStore.get("elements")) {
    let content = (" " + obj.content).trim();
    obj.child.innerHTML = content;

    const elemRect = obj.element.getClientRects()[0];
    const elemWidth = elemRect.width;
    const elemHeight = elemRect.height;

    let rect = obj.child.getClientRects()[0];
    while (rect.height > elemHeight) {
      let idx = content.lastIndexOf(" ");
      if (idx === -1) {
        break;
      }
      content = content.slice(0, idx).trim();
      obj.child.innerHTML = content + "...";
      rect = obj.child.getClientRects()[0];
    }
    while (rect.width > elemWidth) {
      let t_content = content.slice(0, -5);
      if (t_content.length < 5) {
        content = content.slice(0, 5);
        obj.child.innerHTML = content + "...";
        break;
      }
      content = t_content;
      obj.child.innerHTML = content + "...";
      rect = obj.child.getClientRects()[0];
    }
  }
});

// Throttle resize events
window.blxMasonryStore.set("throttleResizeEvents", (evtType) => {
  if (!window.blxMasonryStore.get("resize_lock")) {
    window.blxMasonryStore.set("resize_lock", true);
    window.requestAnimationFrame(() => {
      // Ellipsify text
      window.blxMasonryStore.get("ellipsify")();

      // Dispatch throttled resize event
      window.dispatchEvent(
        new CustomEvent("blx-resize", {
          detail: {
            size: window.blxMasonryStore.get("resize_value"),
          },
        })
      );
      window.blxMasonryStore.set("resize_lock", false);
    });
  }
});

// Get all tile elements
// Add event listeners for mouseenter and mouseleave
for (const element of document.getElementsByClassName(
  "blx__tile__outer_container"
)) {
  const f_enter = () => {
    window.blxMasonryStore.set("current_tile", element);
  };
  const f_leave = () => {
    window.blxMasonryStore.set("current_tile", null);
    const z_counter = window.blxMasonryStore.get("z_counter") + 1;
    window.blxMasonryStore.set("z_counter", z_counter);
    for (const tile of window.blxMasonryStore.get("tiles")) {
      if (tile.element !== element) {
        tile.z_counter = z_counter;
        tile.element.setAttribute("style", "--_z:" + z_counter + ";");
      }
    }
    clearTimeout(window.blxMasonryStore.get("z_counter_resetTimeout"));
    window.blxMasonryStore.set(
      "current_tile",
      setTimeout(window.blxMasonryStore.get("resetZCounter"), 2000)
    );
  };
  window.blxMasonryStore.get("tiles").push({
    element: element,
    z_counter: 1,
    f_enter: f_enter,
    f_leave: f_leave,
  });
  element.addEventListener("mouseenter", f_enter);
  element.addEventListener("mouseleave", f_leave);
}

// Get all elements with class 'clamp' and store them in a Map
for (const element of document.getElementsByClassName("clamp")) {
  const rects = element.getClientRects();
  const child = element.firstElementChild;

  const obj = {
    element: element,
    child: child,
    content: child.innerHTML,
    height: rects[0].height,
  };
  window.blxMasonryStore.get("elements").push(obj);
  window.blxMasonryStore.get("elements_map").set(element, obj);
}

// Add event listener for resize event
window.addEventListener("resize", (evt) => {
  window.blxMasonryStore.set("resize_value", {
    width: window.innerWidth,
    height: window.innerHeight,
  });
  window.blxMasonryStore.get("throttleResizeEvents")("resize");
});

// Initial ellipsifying of text
window.blxMasonryStore.get("ellipsify")();
