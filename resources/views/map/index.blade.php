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
            max-width: 300px;
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
            margin-right: 8px;
            margin-bottom: 8px;
        }
        .map-controls button:hover {
            background: #0056b3;
        }
        .map-controls button.secondary {
            background: #6c757d;
        }
        .map-controls button.secondary:hover {
            background: #545b62;
        }
        .loading-indicator {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
        }
        .user-info {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #dee2e6;
            font-size: 14px;
            color: #666;
        }
        .user-email {
            font-weight: 600;
            color: #333;
        }
        
        /* Auth Modal */
        .auth-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        .auth-modal.active {
            display: flex;
        }
        .auth-content {
            background: white;
            padding: 32px;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .auth-content h2 {
            margin: 0 0 24px 0;
            font-size: 24px;
        }
        .auth-form input {
            width: 100%;
            padding: 12px;
            margin-bottom: 16px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .auth-form button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 12px;
        }
        .auth-form button:hover {
            background: #0056b3;
        }
        .auth-toggle {
            text-align: center;
            margin-top: 16px;
            color: #666;
            font-size: 14px;
        }
        .auth-toggle a {
            color: #007bff;
            cursor: pointer;
            text-decoration: none;
        }
        .auth-toggle a:hover {
            text-decoration: underline;
        }
        .auth-error {
            color: #dc3545;
            font-size: 14px;
            margin-bottom: 12px;
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
        
        .memory-actions {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid rgba(255,255,255,0.2);
            display: flex;
            gap: 16px;
        }
        
        .action-btn {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 20px;
            color: white;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        
        .action-btn:hover {
            background: rgba(255,255,255,0.2);
            transform: scale(1.05);
        }
        
        .action-btn.liked {
            background: rgba(239, 68, 68, 0.3);
            border-color: rgba(239, 68, 68, 0.5);
        }
        
        .action-btn .icon {
            font-size: 18px;
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
        <div id="authButtons">
            <button onclick="showAuthModal('login')">Log In</button>
            <button onclick="showAuthModal('signup')" class="secondary">Sign Up</button>
        </div>
        <div id="userControls" style="display: none;">
            <button onclick="resetView()">Reset View</button>
            <button onclick="logout()" class="secondary">Log Out</button>
            <div class="user-info">
                Logged in as: <span class="user-email" id="userEmail"></span>
            </div>
        </div>
        <div class="loading-indicator" id="loadingIndicator">Log in to view memories</div>
    </div>
    
    <div id="map"></div>
    
    <!-- Auth Modal -->
    <div class="auth-modal" id="authModal">
        <div class="auth-content">
            <h2 id="authTitle">Log In</h2>
            <div class="auth-error" id="authError" style="display: none;"></div>
            <form class="auth-form" id="authForm" onsubmit="handleAuth(event)">
                <input type="email" id="authEmail" placeholder="Email" required>
                <input type="password" id="authPassword" placeholder="Password" required>
                <button type="submit" id="authSubmit">Log In</button>
            </form>
            <div class="auth-toggle">
                <span id="authToggleText">Don't have an account?</span>
                <a onclick="toggleAuthMode()" id="authToggleLink">Sign up</a>
            </div>
        </div>
    </div>
    
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
        // Firebase configuration
        const FIREBASE_CONFIG = {
            apiKey: "AIzaSyCpzODN0eLUm_Ah67-Dx9scaVOdOR4NZX8",
            projectId: "memoir-e284a"
        };
        
        let memories = [];
        let map;
        const markers = [];
        const clusterMarkers = [];
        const CLUSTER_THRESHOLD_PIXELS = 50;
        let currentUser = null;
        let authMode = 'login';
        const likedMemories = new Set();

        // Hardcoded auth
        async function handleAuth(event) {
            event.preventDefault();
            
            const email = document.getElementById('authEmail').value;
            const password = document.getElementById('authPassword').value;
            const errorEl = document.getElementById('authError');
            
            errorEl.style.display = 'none';
            
            if (authMode === 'signup') {
                alert('Account created! Please log in.');
                toggleAuthMode();
                return;
            }
            
            // Hardcoded credentials
            if (email === 'vandalier6@gmail.com' && password === 'WowVandalier!') {
                currentUser = {
                    id: 'user123',
                    email: email,
                    token: 'fake-token-123'
                };
                
                hideAuthModal();
                updateUIForAuth();
                await loadMemories();
            } else {
                errorEl.textContent = 'Invalid credentials';
                errorEl.style.display = 'block';
            }
        }
        
        function showAuthModal(mode) {
            authMode = mode;
            const modal = document.getElementById('authModal');
            const title = document.getElementById('authTitle');
            const submit = document.getElementById('authSubmit');
            const toggleText = document.getElementById('authToggleText');
            const toggleLink = document.getElementById('authToggleLink');
            const errorEl = document.getElementById('authError');
            
            errorEl.style.display = 'none';
            document.getElementById('authForm').reset();
            
            if (mode === 'login') {
                title.textContent = 'Log In';
                submit.textContent = 'Log In';
                toggleText.textContent = "Don't have an account?";
                toggleLink.textContent = 'Sign up';
            } else {
                title.textContent = 'Sign Up';
                submit.textContent = 'Sign Up';
                toggleText.textContent = 'Already have an account?';
                toggleLink.textContent = 'Log in';
            }
            
            modal.classList.add('active');
        }
        
        function hideAuthModal() {
            document.getElementById('authModal').classList.remove('active');
        }
        
        function toggleAuthMode() {
            authMode = authMode === 'login' ? 'signup' : 'login';
            showAuthModal(authMode);
        }
        
        function updateUIForAuth() {
            document.getElementById('authButtons').style.display = 'none';
            document.getElementById('userControls').style.display = 'block';
            document.getElementById('userEmail').textContent = currentUser.email;
        }
        
        function logout() {
            currentUser = null;
            
            document.getElementById('authButtons').style.display = 'block';
            document.getElementById('userControls').style.display = 'none';
            document.getElementById('loadingIndicator').textContent = 'Log in to view memories';
            document.getElementById('loadingIndicator').style.display = 'block';
            
            clearMarkers();
            memories = [];
        }

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
            if (!currentUser) {
                return [];
            }
            
            const loadingEl = document.getElementById('loadingIndicator');
            loadingEl.style.display = 'block';
            
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
                        moodValue: parseInt(fields.moodValue?.integerValue || 0),
                        likes: Math.floor(Math.random() * 50) + 5
                    };
                })
                .filter(m => m.latitude !== 0 && m.longitude !== 0);
                
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
        
        async function loadMemories() {
            memories = await fetchMemories();
            
            if (memories.length === 0) {
                console.log('No memories to display');
                return;
            }
            
            await renderMarkers();
            console.log(`Rendered ${markers.length + clusterMarkers.length} markers`);
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
            
            const groupedMemories = groupMemoriesByPosition(memories);
            const clusters = await performScreenSpaceClustering(groupedMemories);
            
            clusters.forEach(cluster => {
                const el = document.createElement('div');
                
                if (cluster.count > 1) {
                    el.className = 'cluster-marker';
                    el.textContent = cluster.count;
                    
                    const marker = new maplibregl.Marker({ element: el })
                        .setLngLat([cluster.lng, cluster.lat])
                        .addTo(map);
                    
                    el.addEventListener('click', () => {
                        showMemoryDetails(cluster.memories);
                    });
                    
                    markers.push(marker);
                } else {
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

        map.on('load', async () => {
            console.log('Map loaded successfully!');
        });

        map.on('moveend', async () => {
            if (currentUser && memories.length > 0) {
                await renderMarkers();
            }
        });

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

        let currentSlideIndex = 0;
        let currentMemories = [];
        
        function toggleLike(memoryId) {
            const btn = document.querySelector(`[data-memory-id="${memoryId}"]`);
            if (!btn) return;
            
            const countSpan = btn.querySelector('.like-count');
            let currentCount = parseInt(countSpan.textContent);
            
            if (likedMemories.has(memoryId)) {
                likedMemories.delete(memoryId);
                btn.classList.remove('liked');
                countSpan.textContent = currentCount - 1;
            } else {
                likedMemories.add(memoryId);
                btn.classList.add('liked');
                countSpan.textContent = currentCount + 1;
            }
        }
        
        function showMemoryDetails(memoriesAtLocation) {
            currentMemories = memoriesAtLocation;
            currentSlideIndex = 0;
            
            const panel = document.getElementById('sidePanel');
            const content = document.getElementById('panelContent');
            
            let html = '<div class="reel-container" id="reelContainer">';
            
            memoriesAtLocation.forEach((memory) => {
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
                
                const isLiked = likedMemories.has(memory.id);
                const likeCount = memory.likes + (isLiked ? 1 : 0);
                
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
                                <div class="memory-actions">
                                    <button class="action-btn ${isLiked ? 'liked' : ''}" data-memory-id="${memory.id}" onclick="toggleLike('${memory.id}')">
                                        <span class="icon">${isLiked ? '❤️' : '🤍'}</span>
                                        <span class="like-count">${likeCount}</span>
                                    </button>
                                    <button class="action-btn">
                                        <span class="icon">💬</span>
                                        <span>0</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            
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

        function closePanel() {
            const panel = document.getElementById('sidePanel');
            panel.classList.remove('active');
        }

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