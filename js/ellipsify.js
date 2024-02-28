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
