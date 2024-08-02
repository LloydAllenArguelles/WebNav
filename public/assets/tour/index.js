/*
 * Copyright 2016 Google Inc. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
'use strict';

(function() {
  
  var Marzipano = window.Marzipano;
  var bowser = window.bowser;
  var screenfull = window.screenfull;
  var data = window.APP_DATA;

  // Grab elements from DOM.
  var panoElement = document.querySelector('#pano');
  var sceneNameElement = document.querySelector('#titleBar .sceneName');
  var targetNameElement = document.querySelector('#targetBar .targetName');
  var notifyElement = document.querySelector('#targetNotify');
  var sceneListElement = document.querySelector('#sceneList');
  var targetListElement = document.querySelector('#targetList');
  var sceneElements = document.querySelectorAll('#sceneList .scene');
  var sceneListToggleElement = document.querySelector('#sceneListToggle');
  var targetListToggleElement = document.querySelector('#targetListToggle');
  var autorotateToggleElement = document.querySelector('#autorotateToggle');
  var fullscreenToggleElement = document.querySelector('#fullscreenToggle');
  var legendToggleElement = document.querySelector('#legendToggle');
  var legendWindow = document.querySelector('#userManual');
  const targets = document.querySelectorAll('a.target');
  let currentTarget = "61-gv-1f-office";
  let currentScene = null;
  
  // Detect desktop or mobile mode.
  if (window.matchMedia) {
    var setMode = function() {
      if (mql.matches) {
        document.body.classList.remove('desktop');
        document.body.classList.add('mobile');
      } else {
        document.body.classList.remove('mobile');
        document.body.classList.add('desktop');
      }
    };
    var mql = matchMedia("(max-width: 700px), (max-height: 700px)");
    setMode();
    mql.addListener(setMode);
  } else {
    document.body.classList.add('desktop');
  }

  // Detect whether we are on a touch device.
  document.body.classList.add('no-touch');
  window.addEventListener('touchstart', function() {
    document.body.classList.remove('no-touch');
    document.body.classList.add('touch');
  });

  // Use tooltip fallback mode on IE < 11.
  if (bowser.msie && parseFloat(bowser.version) < 11) {
    document.body.classList.add('tooltip-fallback');
  }

  // Viewer options.
  var viewerOpts = {
    controls: {
      mouseViewMode: data.settings.mouseViewMode
    }
  };

  // Initialize viewer.
  var viewer = new Marzipano.Viewer(panoElement, viewerOpts);

  // Create scenes.
  var scenes = data.scenes.map(function(data) {
    var urlPrefix = "tiles";
    var source = Marzipano.ImageUrlSource.fromString(
      urlPrefix + "/" + data.id + "/{z}/{f}/{y}/{x}.jpg",
      { cubeMapPreviewUrl: urlPrefix + "/" + data.id + "/preview.jpg" });
    var geometry = new Marzipano.CubeGeometry(data.levels);

    var limiter = Marzipano.RectilinearView.limit.traditional(data.faceSize, 100*Math.PI/180, 120*Math.PI/180);
    var view = new Marzipano.RectilinearView(data.initialViewParameters, limiter);

    var scene = viewer.createScene({
      source: source,
      geometry: geometry,
      view: view,
      pinFirstLevel: true
    });

    // Create link hotspots.
    data.linkHotspots.forEach(function(hotspot) {
      var element = createLinkHotspotElement(hotspot);
      scene.hotspotContainer().createHotspot(element, { yaw: hotspot.yaw, pitch: hotspot.pitch });
    });

    // Create info hotspots.
    data.infoHotspots.forEach(function(hotspot) {
      if (userId) {
      var element = createInfoHotspotElement(hotspot);
      scene.hotspotContainer().createHotspot(element, { yaw: hotspot.yaw, pitch: hotspot.pitch });
    }
    });

    // Create page hotspots.
    data.pageHotspots.forEach(function(hotspot) {
      var element = createPageHotspotElement(hotspot);
      scene.hotspotContainer().createHotspot(element, { yaw: hotspot.yaw, pitch: hotspot.pitch });
    });

    return {
      data: data,
      scene: scene,
      view: view
    };
  });

  // Set up autorotate, if enabled.
  var autorotate = Marzipano.autorotate({
    yawSpeed: 0.03,
    targetPitch: 0,
    targetFov: Math.PI/2
  });
  if (data.settings.autorotateEnabled) {
    autorotateToggleElement.classList.add('enabled');
  }

  // Set handler for autorotate toggle.
  autorotateToggleElement.addEventListener('click', toggleAutorotate);

  // Set up fullscreen mode, if supported.
  if (screenfull.enabled && data.settings.fullscreenButton) {
    document.body.classList.add('fullscreen-enabled');
    fullscreenToggleElement.addEventListener('click', function() {
      screenfull.toggle();
    });
    screenfull.on('change', function() {
      if (screenfull.isFullscreen) {
        fullscreenToggleElement.classList.add('enabled');
      } else {
        fullscreenToggleElement.classList.remove('enabled');
      }
    });
  } else {
    document.body.classList.add('fullscreen-disabled');
  }

  // Set up legend, if enabled.
  if (data.settings.legendButton) {
    document.body.classList.add('legend-enabled');
    legendToggleElement.addEventListener('click', toggleLegend);
  }

  // Set handler for legend toggle.
  legendToggleElement.addEventListener('click', toggleLegend);

  // Set handler for scene list toggle.
  legendWindow.addEventListener('click', toggleLegend);

  // Set handler for scene list toggle.
  sceneListToggleElement.addEventListener('click', toggleSceneList);

  // Set handler for target list toggle.
  targetListToggleElement.addEventListener('click', toggleTargetList);

  let previousPath = [];

  const updateTargetName = (target) => {
    const targetText = target.querySelector('.text').textContent;
    targetNameElement.textContent = targetText;
    currentTarget = target.getAttribute('data-id');
    
    document.querySelectorAll('.link-hotspot').forEach(element => {
      element.classList.remove('pathing');
      element.classList.remove('visited');
    });

    // Remove 'current' class from previously selected target
    document.querySelectorAll('.target').forEach(t => t.classList.remove('current'));
  
    // Add 'current' class to the currently selected target
    target.classList.add('current');
  
    const { distances, prevNodes } = dijkstra(graph, currentScene);
    const path = [];
    let node = currentTarget;
  
    while (node) {
      path.unshift(node);
      node = prevNodes[node];
    }
  
    console.log('Shortest path:', path);
  
    // Remove 'visited' class from previously visited path
    previousPath.forEach(sceneId => {
      const sceneElement = document.querySelector(`#sceneList .scene[data-id="${sceneId}"]`);
    });
  
    // Switch to the scenes along the new path and add 'visited' class    
    path.forEach(sceneId => {
        const sceneElement = document.querySelector(`#sceneList .scene[data-id="${sceneId}"]`);
        if (sceneElement) {

            // Find elements within #pano with the class "hotspot link-hotspot"
            const hotspotElements = document.querySelectorAll('#pano .hotspot.link-hotspot');

            // Apply 'pathing' class to matching elements
            hotspotElements.forEach(element => {
                if (element.getAttribute('data-target') === sceneId) {
                    element.classList.add('pathing');
                }
            });
        }
    });
  
    // Update the previously visited path
    previousPath = path;
  };

  // Handle target selection
  var targetListElement = document.querySelector('#targetList');
  targetListElement.addEventListener('click', function(event) {
    if (event.target && event.target.matches('a.target')) {
      updateTargetText(event.target.innerText);
    }
  });

  // Set handler for scene switch.
  scenes.forEach(function(scene) {
    var el = document.querySelector('#sceneList .scene[data-id="' + scene.data.id + '"]');
    el.addEventListener('click', function() {
      switchScene(scene);
      toggleNotify();
      // On mobile, hide scene list after selecting a scene.
      if (document.body.classList.contains('mobile')) {
        hideSceneList();
      }
    });
  });

  // Add event listeners to each target
  targets.forEach(target => {
    target.addEventListener('click', () => {
      updateTargetName(target);
    });
  });

  // DOM elements for view controls.
  var viewUpElement = document.querySelector('#viewUp');
  var viewDownElement = document.querySelector('#viewDown');
  var viewLeftElement = document.querySelector('#viewLeft');
  var viewRightElement = document.querySelector('#viewRight');
  var viewInElement = document.querySelector('#viewIn');
  var viewOutElement = document.querySelector('#viewOut');

  // Dynamic parameters for controls.
  var velocity = 0.7;
  var friction = 3;

  // Associate view controls with elements.
  var controls = viewer.controls();
  controls.registerMethod('upElement',    new Marzipano.ElementPressControlMethod(viewUpElement,     'y', -velocity, friction), true);
  controls.registerMethod('downElement',  new Marzipano.ElementPressControlMethod(viewDownElement,   'y',  velocity, friction), true);
  controls.registerMethod('leftElement',  new Marzipano.ElementPressControlMethod(viewLeftElement,   'x', -velocity, friction), true);
  controls.registerMethod('rightElement', new Marzipano.ElementPressControlMethod(viewRightElement,  'x',  velocity, friction), true);
  controls.registerMethod('inElement',    new Marzipano.ElementPressControlMethod(viewInElement,  'zoom', -velocity, friction), true);
  controls.registerMethod('outElement',   new Marzipano.ElementPressControlMethod(viewOutElement, 'zoom',  velocity, friction), true);

  function sanitize(s) {
    return s.replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;');
  }

  function switchScene(scene) {
    stopAutorotate();
    scene.view.setParameters(scene.data.initialViewParameters);
    scene.scene.switchTo();
    startAutorotate();
    updateSceneName(scene);
    updateSceneList(scene);
    console.log(scene.data.id);
    currentScene = scene.data.id;
    const sceneInfo = extractSceneInfo(currentScene);
    console.log(extractSceneInfo(sceneInfo));
  }

  function updateSceneName(scene) {
    sceneNameElement.innerHTML = sanitize(scene.data.name);
  }

  function updateSceneList(scene) {
    for (var i = 0; i < sceneElements.length; i++) {
      var el = sceneElements[i];
      if (el.getAttribute('data-id') === scene.data.id) {
        el.classList.add('current');
      } else {
        el.classList.remove('current');
      }
    }
  }

  function showSceneList() {
    sceneListElement.classList.add('enabled');
    sceneListToggleElement.classList.add('enabled');
  }

  function hideSceneList() {
    sceneListElement.classList.remove('enabled');
    sceneListToggleElement.classList.remove('enabled');
  }

  function toggleSceneList() {
    sceneListElement.classList.toggle('enabled');
    sceneListToggleElement.classList.toggle('enabled');
    if(targetListElement.classList.contains('enabled')) {
      targetListElement.classList.remove('enabled');
      targetListToggleElement.classList.remove('enabled');
    }
    if (notifyElement.classList.contains('enabled')) {
    notifyElement.classList.remove('enabled');
    }
  }

  function toggleTargetList() {
    targetListElement.classList.toggle('enabled');
    targetListToggleElement.classList.toggle('enabled');
    if(sceneListElement.classList.contains('enabled')) {
      sceneListElement.classList.remove('enabled');
      sceneListToggleElement.classList.remove('enabled');
    }
    if (notifyElement.classList.contains('enabled')) {
    notifyElement.classList.remove('enabled');
    }
  }

  function startAutorotate() {
    if (!autorotateToggleElement.classList.contains('enabled')) {
      return;
    }
    viewer.startMovement(autorotate);
    viewer.setIdleMovement(3000, autorotate);
  }

  function stopAutorotate() {
    viewer.stopMovement();
    viewer.setIdleMovement(Infinity);
  }

  function toggleAutorotate() {
    if (autorotateToggleElement.classList.contains('enabled')) {
      autorotateToggleElement.classList.remove('enabled');
      stopAutorotate();
    } else {
      autorotateToggleElement.classList.add('enabled');
      startAutorotate();
    }
  }

  function toggleLegend() {
    if (legendToggleElement.classList.contains('enabled')) {
      legendToggleElement.classList.remove('enabled');
      legendWindow.classList.remove('enabled');
    } else {
      legendToggleElement.classList.add('enabled');
      legendWindow.classList.add('enabled');
    }
  }

  function toggleNotify() {
    if (currentScene == currentTarget){
    notifyElement.classList.add('enabled');
      if (sceneListElement.classList.contains('enabled')||targetListElement.classList.contains('enabled')) {
        sceneListElement.classList.remove('enabled');
        targetListElement.classList.remove('enabled');
      }
    }
  }

  function createLinkHotspotElement(hotspot) {
    var wrapper = document.createElement('div');
    wrapper.classList.add('hotspot');
    wrapper.classList.add('link-hotspot');
    wrapper.setAttribute('data-target', hotspot.target);

    var icon = document.createElement('img');
    icon.src = 'img/link.png';
    icon.classList.add('link-hotspot-icon');

    var transformProperties = ['-ms-transform', '-webkit-transform', 'transform'];
    for (var i = 0; i < transformProperties.length; i++) {
        var property = transformProperties[i];
        icon.style[property] = 'rotate(' + hotspot.rotation + 'rad)';
    }

    wrapper.addEventListener('click', function() {
        wrapper.classList.add('visited');
  
        // Find the previous scene's hotspot element
        const previousSceneId = currentScene;
        notifyElement.classList.remove('enabled');
        switchScene(findSceneById(hotspot.target));
        const hotspotElements = document.querySelectorAll(`#pano .hotspot.link-hotspot[data-target="${previousSceneId}"]`);
        toggleNotify();
        hotspotElements.forEach(element => {
          if (element.getAttribute('data-target') === previousSceneId) {
              element.classList.remove('pathing');
              element.classList.add('visited');
          }
      });
        switchScene(findSceneById(hotspot.target));
        getRoomInfo();
    });

    stopTouchAndScrollEventPropagation(wrapper);

    var tooltip = document.createElement('div');
    tooltip.classList.add('hotspot-tooltip');
    tooltip.classList.add('link-hotspot-tooltip');
    tooltip.innerHTML = findSceneDataById(hotspot.target).name;

    wrapper.appendChild(icon);
    wrapper.appendChild(tooltip);

    return wrapper;
}

  function createInfoHotspotElement(hotspot) {

    // Create wrapper element to hold icon and tooltip.
    var wrapper = document.createElement('div');
    wrapper.classList.add('hotspot');
    wrapper.classList.add('info-hotspot');

    // Create hotspot/tooltip header.
    var header = document.createElement('div');
    header.classList.add('info-hotspot-header');

    // Create image element.
    var iconWrapper = document.createElement('div');
    iconWrapper.classList.add('info-hotspot-icon-wrapper');
    var icon = document.createElement('img');
    icon.src = 'img/info.png';
    icon.classList.add('info-hotspot-icon');
    iconWrapper.appendChild(icon);

    // Create title element.
    var titleWrapper = document.createElement('div');
    titleWrapper.classList.add('info-hotspot-title-wrapper');
    var title = document.createElement('div');
    title.classList.add('info-hotspot-title');
    title.innerHTML = hotspot.title;
    titleWrapper.appendChild(title);

    // Create close element.
    var closeWrapper = document.createElement('div');
    closeWrapper.classList.add('info-hotspot-close-wrapper');
    var closeIcon = document.createElement('img');
    closeIcon.src = 'img/close.png';
    closeIcon.classList.add('info-hotspot-close-icon');
    closeWrapper.appendChild(closeIcon);

    // Construct header element.
    header.appendChild(iconWrapper);
    header.appendChild(titleWrapper);
    header.appendChild(closeWrapper);
    
    var text = document.createElement('div');
    text.classList.add('info-hotspot-text');
    text.innerHTML = hotspot.text;
    displaySchedule(data);

    // Place header and text into wrapper element.
    wrapper.appendChild(header);
    wrapper.appendChild(text);

    // Create a modal for the hotspot content to appear on mobile mode.
    var modal = document.createElement('div');
    modal.innerHTML = wrapper.innerHTML;
    modal.classList.add('info-hotspot-modal');
    document.body.appendChild(modal);

    var toggle = function() {
      wrapper.classList.toggle('visible');
      modal.classList.toggle('visible');
    };

    // Show content when hotspot is clicked.
    wrapper.querySelector('.info-hotspot-header').addEventListener('click', toggle);

    // Hide content when close icon is clicked.
    modal.querySelector('.info-hotspot-close-wrapper').addEventListener('click', toggle);

    // Prevent touch and scroll events from reaching the parent element.
    // This prevents the view control logic from interfering with the hotspot.
    stopTouchAndScrollEventPropagation(wrapper);

    return wrapper;
  }

  function createPageHotspotElement(hotspot) {
    // Create wrapper element to hold icon and tooltip.
    var wrapper = document.createElement('div');
    wrapper.classList.add('hotspot');
    wrapper.classList.add('link-hotspot');
  
    // Create image element.
    var icon = document.createElement('img');
    icon.src = 'img/link.png';
    icon.classList.add('link-hotspot-icon');
  
    // Set rotation transform.
    var transformProperties = [ '-ms-transform', '-webkit-transform', 'transform' ];
    for (var i = 0; i < transformProperties.length; i++) {
      var property = transformProperties[i];
      icon.style[property] = 'rotate(' + hotspot.rotation + 'rad)';
    }
  
    // Add click event handler.
    wrapper.addEventListener('click', function() {
      window.location.href = hotspot.page;
    });
  
    // Prevent touch and scroll events from reaching the parent element.
    // This prevents the view control logic from interfering with the hotspot.
    stopTouchAndScrollEventPropagation(wrapper);
  
    // Create tooltip element.
    var tooltip = document.createElement('div');
    tooltip.classList.add('hotspot-tooltip');
    tooltip.classList.add('link-hotspot-tooltip');
    tooltip.innerHTML = hotspot.pageText;
  
    wrapper.appendChild(icon);
    wrapper.appendChild(tooltip);
  
    return wrapper;
  }

  
  function getRoomInfo() {
  const selectedDay = getDayOfWeek();
  const selectedRoom = extractSceneInfo(currentScene);

  // AJAX request to PHP script
  fetch('../../includes/fetch_schedule_view.php', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json'
      },
      body: JSON.stringify({ room: selectedRoom, day: selectedDay })
  })
  .then(response => {
      if (!response.ok) {
          throw new Error('Network response was not ok ' + response.statusText);
      }
      return response.json();
  })
  .then(data => {
      if (data.error) {
          console.error('Error from server:', data.error);
      } else {
          // Handle the response data (display it in the desired format)
          displaySchedule(data);
      }
  })
  .catch(error => {
      console.error('Error:', error);
      alert('Error: ' + error.message);
  });
}

  function extractSceneInfo(currentScene) {
    if (!currentScene) {
      return null; // Return null if currentScene is null or undefined
    }
    console.log("OWAH " + currentScene); // Log the received scene
  
    // Adjusted regex to allow alphanumeric room identifiers with hyphens
    const match = currentScene.match(/\b(GEE|GCA|GV)-[\w-]+-(\d{3})\b/i);
    if (match) {
      const buildingCode = match[1].toUpperCase();
      const roomNumber = match[2];
      const result = `${buildingCode} ${roomNumber}`;
      console.log("Results:"+result); // Log the matched building code and room number
      return result; // Return the formatted result
    }
  
    console.log(match); // Log null if no match
    return null; // Return null if the pattern doesn't match
  }

  function getDayOfWeek() {
    const date = new Date();
    const options = { weekday: 'long' };
    const dayOfWeek = new Intl.DateTimeFormat('en-US', options).format(date);
    return dayOfWeek;
  }
  
  function displaySchedule(data) {
    // Assuming you have an element to display the schedule
    const scheduleElements = document.querySelectorAll('.info-hotspot-text');
    scheduleElements.innerHTML = '';
    // Loop through each schedule element
    scheduleElements.forEach(scheduleElement => {
      scheduleElement.innerHTML = ''; // Clear previous content

      if (data.length > 0) {
          const table = document.createElement('table');
          table.classList.add('schedule-table');

          const headerRow = document.createElement('tr');
          headerRow.innerHTML = '<th>Time</th><th>Subject</th><th>Instructor</th>';
          table.appendChild(headerRow);

          data.forEach(schedule => {
              const row = document.createElement('tr');
              row.innerHTML = `<td>${schedule.time}</td><td>${schedule.subject}</td><td>${schedule.instructor}</td>`;
              table.appendChild(row);
          });

          scheduleElement.appendChild(table);
      } else {
          scheduleElement.textContent = 'No schedules available for the selected room and day.';
      }
  });
}

  // Prevent touch and scroll events from reaching the parent element.
  function stopTouchAndScrollEventPropagation(element, eventList) {
    var eventList = [ 'touchstart', 'touchmove', 'touchend', 'touchcancel',
                      'wheel', 'mousewheel' ];
    for (var i = 0; i < eventList.length; i++) {
      element.addEventListener(eventList[i], function(event) {
        event.stopPropagation();
      });
    }
  }

  function findSceneById(id) {
    for (var i = 0; i < scenes.length; i++) {
      if (scenes[i].data.id === id) {
        return scenes[i];
      }
    }
    return null;
  }

  function findSceneDataById(id) {
    for (var i = 0; i < data.scenes.length; i++) {
      if (data.scenes[i].id === id) {
        return data.scenes[i];
      }
    }
    return null;
  }  
  function buildGraph(data) {
    const graph = {};
  
    data.scenes.forEach(scene => {
      graph[scene.id] = [];
  
      scene.linkHotspots.forEach(hotspot => {
        graph[scene.id].push({ target: hotspot.target, cost: 1 }); // Assuming cost is 1 for each link
        graph[scene.id].push({ target: hotspot.page, cost: 1 }); // Assuming cost is 1 for each link
      });
    });
  
    return graph;
  }
  
  const graph = buildGraph(APP_DATA);
  function dijkstra(graph, startNode) {
    const distances = {};
    const prevNodes = {};
    const pq = new PriorityQueue((a, b) => distances[a] < distances[b]);
  
    Object.keys(graph).forEach(node => {
      distances[node] = Infinity;
      prevNodes[node] = null;
    });
  
    distances[startNode] = 0;
    pq.enqueue(startNode);
  
    while (!pq.isEmpty()) {
      const currentNode = pq.dequeue();
      const currentDist = distances[currentNode];
  
      graph[currentNode].forEach(neighbor => {
        const distance = currentDist + neighbor.cost;
  
        if (distance < distances[neighbor.target]) {
          distances[neighbor.target] = distance;
          prevNodes[neighbor.target] = currentNode;
          pq.enqueue(neighbor.target);
        }
      });
    }
  
    return { distances, prevNodes };
  }
  
  class PriorityQueue {
    constructor(comparator = (a, b) => a > b) {
      this._heap = [];
      this._comparator = comparator;
    }
  
    size() {
      return this._heap.length;
    }
  
    isEmpty() {
      return this.size() === 0;
    }
  
    peek() {
      return this._heap[0];
    }
  
    enqueue(value) {
      this._heap.push(value);
      this._siftUp();
    }
  
    dequeue() {
      const poppedValue = this.peek();
      const bottom = this.size() - 1;
      if (bottom > 0) {
        this._swap(0, bottom);
      }
      this._heap.pop();
      this._siftDown();
      return poppedValue;
    }
  
    _siftUp() {
      let node = this.size() - 1;
      while (node > 0 && this._comparator(this._heap[node], this._heap[this._parent(node)])) {
        this._swap(node, this._parent(node));
        node = this._parent(node);
      }
    }
  
    _siftDown() {
      let node = 0;
      while (
        (this._left(node) < this.size() && this._comparator(this._heap[this._left(node)], this._heap[node])) ||
        (this._right(node) < this.size() && this._comparator(this._heap[this._right(node)], this._heap[node]))
      ) {
        let maxChild = (this._right(node) < this.size() && this._comparator(this._heap[this._right(node)], this._heap[this._left(node)])) ?
          this._right(node) : this._left(node);
        this._swap(node, maxChild);
        node = maxChild;
      }
    }
  
    _parent(i) {
      return ((i + 1) >> 1) - 1;
    }
  
    _left(i) {
      return (i << 1) + 1;
    }
  
    _right(i) {
      return (i + 1) << 1;
    }
  
    _swap(i, j) {
      [this._heap[i], this._heap[j]] = [this._heap[j], this._heap[i]];
    }
  }
  
  // Display the initial scene.
  currentScene = scenes[0];
  console.log(currentScene.data.id);
  console.log("WOA");
  switchScene(scenes[0]);
})();

