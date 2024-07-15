'use strict';

// Assuming that `window.APP_DATA` is already defined and contains the scene data
(function() {
  var scenes = window.APP_DATA.scenes;
  var currentSceneId = scenes[0].id;
  console.log(currentSceneId);
  
  
  // Get all target elements
  const targets = document.querySelectorAll('a.target');
  const targetNameElement = document.querySelector('.scnName');
  let currentTarget = null;

  // Function to update the target name
  const updateTargetName = (target) => {
    const targetText = target.querySelector('.text').textContent;
    targetNameElement.textContent = targetText;
    currentTarget = target.getAttribute('data-id');
    highlightPath(currentTarget);
  };
  
  // Create a graph representation of the scenes
  var graph = createGraph(scenes);
  
  // Handle target selection
  var targetListElement = document.querySelector('#targetList');
  targetListElement.addEventListener('click', function(event) {
    if (event.target && event.target.matches('a.target')) {
      var targetId = event.target.getAttribute('data-id');
      updateTargetText(event.target.innerText);
      highlightPath(currentSceneId, targetId);
    }
  });


  function createGraph(scenes) {
    var graph = {};
    scenes.forEach(scene => {
      graph[scene.id] = {};
      scene.linkHotspots.forEach(link => {
        graph[scene.id][link.target] = 1; // Assuming equal weight for all edges
      });
    });

    // Output the graph to the console
    console.log('Graph Representation:');
    Object.keys(graph).forEach(node => {
    console.log(`Node: ${node}`);
    Object.keys(graph[node]).forEach(neighbor => {
    console.log(`  -> ${neighbor}`);
    });
  });
    return graph;
  }

  function updateTargetText(targetText) {
    var targetBar = document.querySelector('#targetBar .targetName');
    targetBar.innerText = targetText;
  }

  function highlightPath(startId, targetId) {
    // Clear previous highlights
    clearHighlights();
    
    // Get the shortest path using Dijkstra's algorithm
    var path = findShortestPath(graph, startId, targetId);
    
    // Log the path to the console
    console.log(`Path from Scene ${startId} to Scene ${targetId}:`);
    console.log(path.join(' -> '));

    // Highlight the path
    path.forEach(sceneId => {
      highlightScene(sceneId, 'red');
    });

    // Update the current scene
    currentSceneId = startId;
  }

  function clearHighlights() {
    document.querySelectorAll('.link-hotspot').forEach(el => {
      el.style.border = 'none';
    });
  }

  function highlightScene(sceneId, color) {
    var scene = findSceneById(sceneId);
    if (scene) {
      scene.linkHotspots.forEach(hotspot => {
        var el = document.querySelector(`.link-hotspot[data-id="${hotspot.target}"]`);
        if (el) {
          el.style.border = `5px solid ${color}`;
        }
      });
    }
  }

  function findSceneById(id) {
    return scenes.find(scene => scene.id === id);
  }

  function findShortestPath(graph, start, end) {
    var distances = {};
    var prev = {};
    var pq = new PriorityQueue();

    distances[start] = 0;
    pq.enqueue(start, 0);

    Object.keys(graph).forEach(node => {
      if (node !== start) distances[node] = Infinity;
      prev[node] = null;
    });

    while (!pq.isEmpty()) {
      var minNode = pq.dequeue().element;

      if (minNode === end) {
        var path = [];
        var u = end;
        while (prev[u]) {
          path.unshift(u);
          u = prev[u];
        }
        path.unshift(start);
        return path;
      }

      if (minNode || distances[minNode] !== Infinity) {
        Object.keys(graph[minNode]).forEach(neighbor => {
          var alt = distances[minNode] + graph[minNode][neighbor];
          if (alt < distances[neighbor]) {
            distances[neighbor] = alt;
            prev[neighbor] = minNode;
            pq.enqueue(neighbor, distances[neighbor]);
          }
        });
      }
    }

    return [];
  }

  class PriorityQueue {
    constructor() {
      this.collection = [];
    }

    enqueue(element, priority) {
      var node = { element, priority };
      if (this.isEmpty()) {
        this.collection.push(node);
      } else {
        var added = false;
        for (var i = 0; i < this.collection.length; i++) {
          if (node.priority < this.collection[i].priority) {
            this.collection.splice(i, 0, node);
            added = true;
            break;
          }
        }
        if (!added) this.collection.push(node);
      }
    }

    dequeue() {
      return this.collection.shift();
    }

    isEmpty() {
      return this.collection.length === 0;
    }
  }

  // Switch scene function
  function switchScene(scene) {
    // Your existing switchScene implementation...
  }

  // Initial setup to display the first scene
  switchScene(findSceneById(currentSceneId));
  
  // Add event listeners to each target
  targets.forEach(target => {
    target.addEventListener('click', () => {
      updateTargetName(target);
    });
  });
})();
