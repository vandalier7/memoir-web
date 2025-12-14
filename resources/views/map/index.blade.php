<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map - Memoir</title>
    
    <!-- MapLibre GL CSS -->
    <link href="https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.css" rel="stylesheet" />
    
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
        #map {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 100%;
        }
        .map-controls {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .map-controls h3 {
            margin: 0 0 10px 0;
            font-size: 18px;
        }
        .map-controls button {
            padding: 8px 16px;
            border: none;
            background: #007bff;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .map-controls button:hover {
            background: #0056b3;
        }
        .loading-indicator {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
        }
        
        /* Side panel - Reel Style */
        .side-panel {
            position: absolute;
            top: 0;
            right: -100%;
            width: 100%;
            max-width: 450px;
            height: 100%;
            background: #000;
            box-shadow: -2px 0 8px rgba(0,0,0,0.15);
            transition: right 0.3s ease;
            z-index: 2;
            overflow: hidden;
        }
        .side-panel.active {
            right: 0;
        }
        .panel-header {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: linear-gradient(to bottom, rgba(0,0,0,0.8), rgba(0,0,0,0));
            z-index: 3;
        }
        .panel-header h2 {
            margin: 0;
            font-size: 20px;
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }
        .close-btn {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: white;
            padding: 0;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        .close-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        .panel-content {
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        /* Reel container */
        .reel-container {
            height: 100%;
            position: relative;
            display: flex;
            transition: transform 0.3s ease;
        }
        
        /* Memory slide */
        .memory-slide {
            min-width: 100%;
            height: 100%;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        
        .memory-image-container {
            flex: 1;
            position: relative;
            overflow: hidden;
            background: #000;
        }
        
        .memory-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
        
        .memory-image.placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 48px;
        }
        
        /* Overlay details */
        .memory-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 24px;
            background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.7) 50%, rgba(0,0,0,0) 100%);
            color: white;
        }
        
        .memory-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 12px 0;
            text-shadow: 0 2px 8px rgba(0,0,0,0.5);
            line-height: 1.3;
        }
        
        .memory-meta {
            color: rgba(255,255,255,0.9);
            font-size: 14px;
            margin-bottom: 8px;
            text-shadow: 0 1px 4px rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .memory-location {
            color: rgba(255,255,255,0.7);
            font-size: 12px;
            font-family: monospace;
            text-shadow: 0 1px 4px rgba(0,0,0,0.5);
            margin-bottom: 12px;
        }
        
        .memory-description {
            margin-top: 12px;
            line-height: 1.6;
            color: rgba(255,255,255,0.95);
            text-shadow: 0 1px 4px rgba(0,0,0,0.5);
            font-size: 15px;
            max-height: 100px;
            overflow-y: auto;
        }
        
        .memory-user {
            color: #60a5fa;
            font-weight: 600;
        }
        
        /* Navigation arrows */
        .reel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 4;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border: none;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            cursor: pointer;
            color: white;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        
        .reel-nav:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-50%) scale(1.1);
        }
        
        .reel-nav:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }
        
        .reel-nav:disabled:hover {
            transform: translateY(-50%);
        }
        
        .reel-nav.prev {
            left: 16px;
        }
        
        .reel-nav.next {
            right: 16px;
        }
        
        /* Counter */
        .reel-counter {
            position: absolute;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(10px);
            padding: 8px 16px;
            border-radius: 20px;
            color: white;
            font-size: 14px;
            font-weight: 500;
            z-index: 3;
        }
        
        /* Custom marker */
        .custom-marker {
            width: 30px;
            height: 30px;
            border: 3px solid white;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            transition: transform 0.2s, opacity 0.3s;
            opacity: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }
        .custom-marker:hover {
            transform: scale(1.2);
        }
        .custom-marker.faded {
            opacity: 0;
        }
        
        /* Cluster marker */
        .cluster-marker {
            width: 40px;
            height: 40px;
            background: #6366f1;
            border: 3px solid white;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.4);
            transition: transform 0.2s, opacity 0.3s;
            opacity: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        .cluster-marker:hover {
            transform: scale(1.15);
        }
        .cluster-marker.faded {
            opacity: 0;
        }
        
        .error-message {
            color: #dc3545;
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="map-controls">
        <h3>Memoir Map</h3>
        <button onclick="resetView()">Reset View</button>
        <div class="loading-indicator" id="loadingIndicator">Loading memories...</div>
    </div>
    
    <div id="map"></div>
    
    <!-- Side Panel -->
    <div class="side-panel" id="sidePanel">
        <div class="panel-header">
            <h2>Memories</h2>
            <button class="close-btn" onclick="closePanel()">&times;</button>
        </div>
        <div class="panel-content" id="panelContent">
            <!-- Memory details will be inserted here -->
        </div>
    </div>

    <!-- MapLibre GL JS -->
    <script src="https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.js"></script>
    
    <script>
        // Firebase and Supabase configuration
        const FIREBASE_CONFIG = {
            apiKey: "AIzaSyCpzODN0eLUm_Ah67-Dx9scaVOdOR4NZX8",
            projectId: "memoir-e284a"
        };
        
        const SUPABASE_URL = "https://drnpxydotpjbxigrnlli.supabase.co";
        const SUPABASE_ANON_KEY = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImRybnB4eWRvdHBqYnhpZ3JubGxpIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjE2NjcwOTMsImV4cCI6MjA3NzI0MzA5M30.jMuA5DoAbWz-WCfcyqg6ndPy1pkxMUXOutj3UbGTptg";
        
        let memories = [];
        let map;
        const markers = [];
        const clusterMarkers = [];
        const CLUSTER_THRESHOLD_PIXELS = 50;

        // Mood system
        const MOODS = {
            0: { name: 'happy', color: '#E2B900' },
            1: { name: 'sad', color: '#2141F3' },
            2: { name: 'angry', color: '#B71C1C' },
            3: { name: 'disgusted', color: '#2E7D32' },
            4: { name: 'afraid', color: '#651FFF' },
            5: { name: 'calm', color: '#42A5F5' },
            6: { name: 'worried', color: '#FF5722' }
        };

        function getMoodColor(moodValue) {
            return MOODS[moodValue]?.color || '#FF4444';
        }

        // Initialize the map (centered on Philippines)
        map = new maplibregl.Map({
            container: 'map',
            style: `https://api.maptiler.com/maps/dataviz-v4/style.json?key=gyEpeYKGmrox3x3xvhNk`,
            center: [121.7740, 12.8797],
            zoom: 5.5
        });

        // Add navigation controls
        map.addControl(new maplibregl.NavigationControl(), 'top-right');
        map.addControl(new maplibregl.ScaleControl(), 'bottom-left');

        // Group memories by exact LatLng
        function groupMemoriesByPosition(memories) {
            const grouped = {};
            
            memories.forEach(memory => {
                const key = `${memory.latitude},${memory.longitude}`;
                if (!grouped[key]) {
                    grouped[key] = [];
                }
                grouped[key].push(memory);
            });
            
            return grouped;
        }

        // Calculate screen distance between two points
        function getScreenDistance(point1, point2) {
            const dx = point1.x - point2.x;
            const dy = point1.y - point2.y;
            return Math.sqrt(dx * dx + dy * dy);
        }

        // Perform screen-space clustering
        async function performScreenSpaceClustering(groupedMemories) {
            const positions = Object.keys(groupedMemories);
            const screenPositions = [];
            
            // Get screen positions for all memory groups
            for (const key of positions) {
                const [lat, lng] = key.split(',').map(Number);
                const screenPos = map.project([lng, lat]);
                screenPositions.push({
                    key,
                    lat,
                    lng,
                    screenPos,
                    memories: groupedMemories[key]
                });
            }
            
            const clusters = [];
            const clustered = new Set();
            
            // Find clusters based on screen distance
            for (let i = 0; i < screenPositions.length; i++) {
                if (clustered.has(i)) continue;
                
                const cluster = [i];
                clustered.add(i);
                
                for (let j = i + 1; j < screenPositions.length; j++) {
                    if (clustered.has(j)) continue;
                    
                    const distance = getScreenDistance(
                        screenPositions[i].screenPos,
                        screenPositions[j].screenPos
                    );
                    
                    if (distance < CLUSTER_THRESHOLD_PIXELS) {
                        cluster.push(j);
                        clustered.add(j);
                    }
                }
                
                if (cluster.length > 1) {
                    // Calculate cluster center
                    let sumLat = 0, sumLng = 0;
                    const allMemories = [];
                    
                    cluster.forEach(idx => {
                        sumLat += screenPositions[idx].lat;
                        sumLng += screenPositions[idx].lng;
                        allMemories.push(...screenPositions[idx].memories);
                    });
                    
                    clusters.push({
                        lat: sumLat / cluster.length,
                        lng: sumLng / cluster.length,
                        memories: allMemories,
                        count: cluster.length
                    });
                } else {
                    // Single position (not clustered)
                    clusters.push({
                        lat: screenPositions[i].lat,
                        lng: screenPositions[i].lng,
                        memories: screenPositions[i].memories,
                        count: 1
                    });
                }
            }
            
            return clusters;
        }

        // Fetch memories from database
        async function fetchMemories() {
            const loadingEl = document.getElementById('loadingIndicator');
            
            try {
                loadingEl.textContent = 'Fetching from Firestore...';
                
                const firestoreUrl = `https://firestore.googleapis.com/v1/projects/${FIREBASE_CONFIG.projectId}/databases/(default)/documents/memories?pageSize=50`;
                
                const firestoreResponse = await fetch(firestoreUrl);
                const firestoreData = await firestoreResponse.json();
                
                if (!firestoreData.documents) {
                    loadingEl.textContent = 'No memories found';
                    loadingEl.classList.add('error-message');
                    return [];
                }
                
                loadingEl.textContent = 'Processing memories...';
                
                const processedMemories = firestoreData.documents.map(doc => {
                    const fields = doc.fields;
                    
                    return {
                        id: doc.name.split('/').pop(),
                        title: fields.addressString?.stringValue || 'Untitled Memory',
                        description: fields.description?.stringValue || '',
                        imageUrl: fields.imageUrl?.stringValue || null,
                        latitude: parseFloat(fields.latitude?.doubleValue || fields.latitude?.integerValue || 0),
                        longitude: parseFloat(fields.longitude?.doubleValue || fields.longitude?.integerValue || 0),
                        userId: fields.userId?.stringValue || 'unknown',
                        userName: fields.userName?.stringValue || 'Anonymous',
                        createdAt: fields.createdAt?.timestampValue || new Date().toISOString(),
                        supabaseMemoryId: fields.supabaseMemoryId?.integerValue || null,
                        moodValue: parseInt(fields.moodValue?.integerValue || 0)
                    };
                }).filter(m => m.latitude !== 0 && m.longitude !== 0);
                
                loadingEl.textContent = `Loaded ${processedMemories.length} memories`;
                setTimeout(() => {
                    loadingEl.style.display = 'none';
                }, 2000);
                
                return processedMemories;
                
            } catch (error) {
                console.error('Error fetching memories:', error);
                loadingEl.textContent = 'Error loading memories';
                loadingEl.classList.add('error-message');
                return [];
            }
        }

        // Clear all markers
        function clearMarkers() {
            markers.forEach(marker => marker.remove());
            markers.length = 0;
            clusterMarkers.forEach(marker => marker.remove());
            clusterMarkers.length = 0;
        }

        // Render markers with clustering
        async function renderMarkers() {
            clearMarkers();
            
            if (memories.length === 0) return;
            
            // Group by exact LatLng
            const groupedMemories = groupMemoriesByPosition(memories);
            
            // Perform screen-space clustering
            const clusters = await performScreenSpaceClustering(groupedMemories);
            
            // Render clusters
            clusters.forEach(cluster => {
                const el = document.createElement('div');
                
                if (cluster.count > 1) {
                    // Screen-space cluster
                    el.className = 'cluster-marker';
                    el.textContent = cluster.count;
                    
                    const marker = new maplibregl.Marker({ element: el })
                        .setLngLat([cluster.lng, cluster.lat])
                        .addTo(map);
                    
                    el.addEventListener('click', () => {
                        map.flyTo({
                            center: [cluster.lng, cluster.lat],
                            zoom: map.getZoom() + 2,
                            essential: true
                        });
                    });
                    
                    clusterMarkers.push(marker);
                } else if (cluster.memories.length > 1) {
                    // LatLng cluster (multiple memories at same location)
                    el.className = 'custom-marker';
                    el.style.background = getMoodColor(cluster.memories[0].moodValue);
                    el.textContent = cluster.memories.length;
                    
                    const marker = new maplibregl.Marker({ element: el })
                        .setLngLat([cluster.lng, cluster.lat])
                        .addTo(map);
                    
                    el.addEventListener('click', () => {
                        showMemoryDetails(cluster.memories);
                    });
                    
                    markers.push(marker);
                } else {
                    // Single memory
                    el.className = 'custom-marker';
                    el.style.background = getMoodColor(cluster.memories[0].moodValue);
                    
                    const marker = new maplibregl.Marker({ element: el })
                        .setLngLat([cluster.lng, cluster.lat])
                        .addTo(map);
                    
                    el.addEventListener('click', () => {
                        showMemoryDetails(cluster.memories);
                    });
                    
                    markers.push(marker);
                }
            });
        }

        // Wait for map to load
        map.on('load', async () => {
            console.log('Map loaded successfully!');
            
            memories = await fetchMemories();
            
            if (memories.length === 0) {
                console.log('No memories to display');
                return;
            }
            
            await renderMarkers();
            console.log(`Rendered ${markers.length + clusterMarkers.length} markers`);
        });

        // Re-cluster on zoom/move end
        map.on('moveend', async () => {
            await renderMarkers();
        });

        // Fade markers when moving
        let moveTimeout;
        
        map.on('movestart', () => {
            document.querySelectorAll('.custom-marker, .cluster-marker').forEach(marker => {
                marker.classList.add('faded');
            });
        });
        
        map.on('moveend', () => {
            clearTimeout(moveTimeout);
            moveTimeout = setTimeout(() => {
                document.querySelectorAll('.custom-marker, .cluster-marker').forEach(marker => {
                    marker.classList.remove('faded');
                });
            }, 200);
        });

        // Show memory details in side panel (reel style)
        let currentSlideIndex = 0;
        let currentMemories = [];
        
        function showMemoryDetails(memoriesAtLocation) {
            currentMemories = memoriesAtLocation;
            currentSlideIndex = 0;
            
            const panel = document.getElementById('sidePanel');
            const content = document.getElementById('panelContent');
            
            // Build reel
            let html = '<div class="reel-container" id="reelContainer">';
            
            memoriesAtLocation.forEach((memory, index) => {
                const formattedDate = new Date(memory.createdAt).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                const imageHtml = memory.imageUrl 
                    ? `<img src="${memory.imageUrl}" alt="${memory.title}" class="memory-image" onerror="this.classList.add('placeholder'); this.innerHTML='📷'">`
                    : '<div class="memory-image placeholder">📷</div>';
                
                html += `
                    <div class="memory-slide">
                        <div class="memory-image-container">
                            ${imageHtml}
                            <div class="memory-overlay">
                                <h3 class="memory-title">${memory.title}</h3>
                                <div class="memory-meta">
                                    <span>👤</span>
                                    <span class="memory-user">${memory.userName}</span>
                                </div>
                                <div class="memory-meta">
                                    <span>📅</span>
                                    <span>${formattedDate}</span>
                                </div>
                                <div class="memory-location">📍 ${memory.latitude.toFixed(4)}, ${memory.longitude.toFixed(4)}</div>
                                ${memory.description ? `<div class="memory-description">${memory.description}</div>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            
            // Add navigation if multiple memories
            if (memoriesAtLocation.length > 1) {
                html += `
                    <button class="reel-nav prev" onclick="navigateReel(-1)" id="prevBtn">‹</button>
                    <button class="reel-nav next" onclick="navigateReel(1)" id="nextBtn">›</button>
                    <div class="reel-counter" id="reelCounter">1 / ${memoriesAtLocation.length}</div>
                `;
            }
            
            content.innerHTML = html;
            panel.classList.add('active');
            
            updateReelNavigation();
            
            // Fly to the memory location
            map.flyTo({
                center: [memoriesAtLocation[0].longitude, memoriesAtLocation[0].latitude],
                zoom: Math.max(map.getZoom(), 14),
                essential: true
            });
        }
        
        function navigateReel(direction) {
            currentSlideIndex += direction;
            currentSlideIndex = Math.max(0, Math.min(currentSlideIndex, currentMemories.length - 1));
            
            const container = document.getElementById('reelContainer');
            container.style.transform = `translateX(-${currentSlideIndex * 100}%)`;
            
            updateReelNavigation();
        }
        
        function updateReelNavigation() {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const counter = document.getElementById('reelCounter');
            
            if (prevBtn) {
                prevBtn.disabled = currentSlideIndex === 0;
            }
            
            if (nextBtn) {
                nextBtn.disabled = currentSlideIndex === currentMemories.length - 1;
            }
            
            if (counter) {
                counter.textContent = `${currentSlideIndex + 1} / ${currentMemories.length}`;
            }
        }

        // Close side panel
        function closePanel() {
            const panel = document.getElementById('sidePanel');
            panel.classList.remove('active');
        }

        // Reset view function
        function resetView() {
            closePanel();
            map.flyTo({
                center: [121.7740, 12.8797],
                zoom: 5.5,
                essential: true
            });
        }
    </script>
</body>
</html>