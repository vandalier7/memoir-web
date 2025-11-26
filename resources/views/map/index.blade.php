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
            padding: 10px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="map-controls">
        <h3>Memoir Map</h3>
        <button onclick="resetView()">Reset View</button>
    </div>
    
    <div id="map"></div>

    <!-- MapLibre GL JS -->
    <script src="https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.js"></script>
    
    <script>
        const apiKey = '{{ $apiKey }}';
        
        // Initialize the map
        const map = new maplibregl.Map({
            container: 'map',
            style: `https://api.maptiler.com/maps/dataviz/style.json?key=${apiKey}`,
            center: [0, 0], // [longitude, latitude]
            zoom: 2
        });

        // Add navigation controls (zoom and rotation)
        map.addControl(new maplibregl.NavigationControl(), 'top-right');

        // Add scale control
        map.addControl(new maplibregl.ScaleControl(), 'bottom-left');

        // Wait for map to load
        map.on('load', () => {
            console.log('Map loaded successfully!');
            
            // Example: Add a marker
            new maplibregl.Marker({color: '#FF0000'})
                .setLngLat([0, 0])
                .setPopup(new maplibregl.Popup().setHTML('<h3>Welcome!</h3><p>This is your MapTiler map</p>'))
                .addTo(map);
        });

        // Example: Add click event to show coordinates
        map.on('click', (e) => {
            console.log(`Coordinates: ${e.lngLat.lng}, ${e.lngLat.lat}`);
            
            // Create a popup at clicked location
            new maplibregl.Popup()
                .setLngLat(e.lngLat)
                .setHTML(`
                    <strong>Location:</strong><br>
                    Lng: ${e.lngLat.lng.toFixed(4)}<br>
                    Lat: ${e.lngLat.lat.toFixed(4)}
                `)
                .addTo(map);
        });

        // Reset view function
        function resetView() {
            map.flyTo({
                center: [0, 0],
                zoom: 2,
                essential: true
            });
        }
    </script>
</body>
</html>