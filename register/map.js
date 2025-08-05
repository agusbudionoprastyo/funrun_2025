mapboxgl.accessToken = 'pk.eyJ1IjoiYWd1c2J1ZGlvbm9wcmFzdHlvIiwiYSI6ImNsdW1mcTZncTBlc3oyaW1tMGlieHBkMmsifQ.CCttupREZDc0WfusBBm09A';

// Buat peta
var map = new mapboxgl.Map({
  container: 'map', // ID elemen HTML tempat peta akan ditampilkan
  style: 'mapbox://styles/mapbox/streets-v11', // Pilih gaya peta
  center: [110.41201948215935, -6.9794677077623986], // Koordinat awal (longitude, latitude)
  zoom: 13
});

// Buat kontrol peta (zoom, compass)
map.addControl(new mapboxgl.NavigationControl());

// Menambahkan elemen untuk menampilkan jarak di atas peta
var distanceElement = document.createElement('div');
distanceElement.className = 'distance-info';
distanceElement.style.position = 'absolute';
distanceElement.style.top = '10px';
distanceElement.style.left = '10px';
distanceElement.style.backgroundColor = 'rgba(255, 255, 255, 0.9)';
distanceElement.style.padding = '10px';
distanceElement.style.borderRadius = '15px';
distanceElement.style.fontSize = '12px';
distanceElement.style.color = 'black';
distanceElement.style.fontWeight = 'bold';
map.getContainer().appendChild(distanceElement);

// Fungsi untuk mendapatkan rute menggunakan Mapbox Directions API
function getRoute() {
  // Array semua titik route: START/FINISH, Marshal 1-14, kembali ke START/FINISH
  const routePoints = [
    [110.41124510648847, -6.979465144525227], // START/FINISH
    [110.4132822844655, -6.9749235700715015], // Marshal 1
    [110.41480694499647, -6.973531583072864], // Marshal 2
    [110.41953248369022, -6.971251919716325], // Marshal 3
    [110.42232579371996, -6.969924453250012], // Marshal 4
    [110.42517418618024, -6.9685237492191305], // Marshal 5
    [110.42511580431477, -6.966584677114943], // Marshal 6
    [110.42925817984032, -6.965291861701476], // Marshal 7
    [110.43008569448317, -6.967665290748463], // Marshal 8
    [110.4252425562517, -6.968728790584302], // Marshal 9
    [110.4228079211141, -6.971106525390175], // Marshal 10
    [110.42013374224155, -6.973821451482721], // Marshal 11
    [110.41674208343511, -6.977250726160813], // Marshal 12
    [110.41565448478823, -6.978286651448628], // Marshal 13
    [110.40971114259015, -6.983524400093781], // Marshal 14
    [110.41124510648847, -6.979465144525227]  // Kembali ke START/FINISH
  ];

  // Build coordinates string for Mapbox Directions API
  const coordinatesStr = routePoints.map(p => `${p[0]},${p[1]}`).join(';');
  const url = `https://api.mapbox.com/directions/v5/mapbox/driving/${coordinatesStr}?alternatives=false&geometries=geojson&steps=true&access_token=${mapboxgl.accessToken}`;

  fetch(url)
    .then(response => response.json())
    .then(data => {
      if (data.routes && data.routes.length > 0) {
        const route = data.routes[0].geometry;
        const distance = data.routes[0].distance / 1000;
        // const distanceText = `Distance ${distance.toFixed(2)} km`;
        const distanceText = `ⓘ Marshal`;
        distanceElement.textContent = distanceText;

        // Hapus layer route lama jika ada
        if (map.getLayer('route')) map.removeLayer('route');
        if (map.getSource('route')) map.removeSource('route');

        // Tambahkan polyline utama
        map.addLayer({
          'id': 'route',
          'type': 'line',
          'source': {
            'type': 'geojson',
            'data': {
              'type': 'Feature',
              'properties': {},
              'geometry': route
            }
          },
          'paint': {
            'line-color': 'rgba(255,0,91, 0.8)',
            'line-width': 4
          }
        });
      }
    })
    .catch(error => {
      console.error('Error fetching route:', error);
    });
}

// Panggil fungsi untuk menampilkan lokasi pengguna dan rute saat peta dimuat
map.on('load', function() {
  getRoute();

  // Marker START/FINISH pakai emoji 🏁 di atas label
  const startMarkerDiv = document.createElement('div');
  startMarkerDiv.style.display = 'flex';
  startMarkerDiv.style.flexDirection = 'column';
  startMarkerDiv.style.alignItems = 'center';
  startMarkerDiv.style.justifyContent = 'center';
  startMarkerDiv.style.background = 'none';
  startMarkerDiv.style.border = 'none';
  startMarkerDiv.style.boxShadow = 'none';
  startMarkerDiv.style.padding = '0';
  const flag = document.createElement('span');
  flag.textContent = '🏁';
  flag.style.fontSize = '28px';
  flag.style.lineHeight = '1';
  flag.style.marginBottom = '-4px';
  startMarkerDiv.appendChild(flag);
  const label = document.createElement('span');
  label.textContent = 'START/FINISH';
  label.style.fontWeight = 'bold';
  label.style.fontSize = '13px';
  label.style.color = 'black';
  label.style.background = 'none';
  label.style.border = 'none';
  label.style.marginTop = '0px';
  startMarkerDiv.appendChild(label);
  new mapboxgl.Marker({ element: startMarkerDiv })
    .setLngLat([110.41124510648847, -6.979465144525227])
    .addTo(map);

  // Array checkpoint marshal
  const marshalCheckpoints = [
    { lng: 110.4132822844655, lat: -6.9749235700715015, name: 'Marshal 1' },
    { lng: 110.41480694499647, lat: -6.973531583072864, name: 'Marshal 2' },
    { lng: 110.41953248369022, lat: -6.971251919716325, name: 'Marshal 3' },
    { lng: 110.42232579371996, lat: -6.969924453250012, name: 'Marshal 4' },
    { lng: 110.42517418618024, lat: -6.9685237492191305, name: 'Marshal 5' },
    { lng: 110.42511580431477, lat: -6.966584677114943, name: 'Marshal 6' },
    { lng: 110.42925817984032, lat: -6.965291861701476, name: 'Marshal 7' },
    { lng: 110.43008569448317, lat: -6.967665290748463, name: 'Marshal 8' },
    { lng: 110.4252425562517, lat: -6.968728790584302, name: 'Marshal 9' },
    { lng: 110.4228079211141, lat: -6.971106525390175, name: 'Marshal 10' },
    { lng: 110.42013374224155, lat: -6.973821451482721, name: 'Marshal 11' },
    { lng: 110.41674208343511, lat: -6.977250726160813, name: 'Marshal 12' },
    { lng: 110.41565448478823, lat: -6.978286651448628, name: 'Marshal 13' },
    { lng: 110.40971114259015, lat: -6.983524400093781, name: 'Marshal 14' }
  ];

  marshalCheckpoints.forEach(cp => {
    // Marker bulat putih isi ⓘ
    const markerDiv = document.createElement('div');
    markerDiv.style.width = '20px';
    markerDiv.style.height = '20px';
    markerDiv.style.backgroundColor = 'rgba(255, 255, 255, 0.9)';
    markerDiv.style.border = '2px solid white';
    markerDiv.style.borderRadius = '50%';
    markerDiv.style.display = 'flex';
    markerDiv.style.alignItems = 'center';
    markerDiv.style.justifyContent = 'center';
    markerDiv.style.fontWeight = 'bold';
    markerDiv.style.fontSize = '12px';
    markerDiv.style.color = 'black';
    markerDiv.style.cursor = 'pointer';
    markerDiv.style.boxShadow = '0 1px 3px rgba(0,0,0,0.3)';
    markerDiv.textContent = 'ⓘ';
    const marker = new mapboxgl.Marker({ element: markerDiv })
      .setLngLat([cp.lng, cp.lat])
      .addTo(map);
    marker.getElement().addEventListener('click', function() {
      // Tampilkan popup nama marshal
      new mapboxgl.Popup()
        .setLngLat([cp.lng, cp.lat])
        .setHTML(`<strong>${cp.name}</strong>`)
        .addTo(map);
    });
  });

  // Water Station
  const waterStations = [
    { lng: 110.42388938448875, lat: -6.969628740201411, name: 'Water Station' },
    { lng: 110.4183192790272, lat: -6.975631995264069, name: 'Water Station' }
  ];
  waterStations.forEach(cp => {
    const markerDiv = document.createElement('div');
    markerDiv.style.width = '80px';
    markerDiv.style.height = '25px';
    markerDiv.style.backgroundColor = 'rgba(255, 255, 255, 0.9)';
    markerDiv.style.border = '2px solid white';
    markerDiv.style.borderRadius = '12px';
    markerDiv.style.display = 'flex';
    markerDiv.style.alignItems = 'center';
    markerDiv.style.justifyContent = 'center';
    markerDiv.style.fontWeight = 'bold';
    markerDiv.style.fontSize = '8px';
    markerDiv.style.color = 'black';
    markerDiv.style.cursor = 'pointer';
    markerDiv.style.boxShadow = '0 1px 3px rgba(0,0,0,0.3)';
    markerDiv.style.padding = '0 6px';
    markerDiv.textContent = cp.name;
    const marker = new mapboxgl.Marker({ element: markerDiv })
      .setLngLat([cp.lng, cp.lat])
      .addTo(map);
    marker.getElement().addEventListener('click', function() {
      new mapboxgl.Popup()
        .setLngLat([cp.lng, cp.lat])
        .setHTML(`<strong>${cp.name}</strong>`)
        .addTo(map);
    });
  });
});

// Fungsi untuk membuat marker dengan gambar PNG
function createCustomMarker(imageUrl, rotate) {
  const markerDiv = document.createElement('div');
  markerDiv.style.backgroundImage = `url(${imageUrl})`; // Ganti dengan gambar PNG yang diinginkan
  markerDiv.style.width = '30px'; // Ukuran marker
  markerDiv.style.height = '30px'; // Ukuran marker
  markerDiv.style.backgroundSize = 'contain'; // Agar gambar tidak terpotong
  markerDiv.style.backgroundRepeat = 'no-repeat'; // Gambar tidak diulang
  markerDiv.style.cursor = 'pointer'; // Cursor pointer saat hover

  // Posisi dan rotasi marker
  markerDiv.style.transform = `rotate(${rotate}deg)`; // Mengatur rotasi
  markerDiv.style.transformOrigin = 'center'; // Titik rotasi di tengah gambar
  
  // Menghilangkan pengaturan offset posisi
  markerDiv.style.position = 'relative'; // Posisi relatif sesuai dengan peta
  
  return markerDiv;
}

// Fungsi untuk membuat elemen teks
function createTextElement(text) {
  const textElement = document.createElement('div');
  textElement.style.position = 'absolute';
  textElement.style.backgroundColor = 'rgba(255, 255, 255, 1)';
  textElement.style.padding = '5px';
  textElement.style.borderRadius = '5px';
  textElement.style.fontSize = '12px';
  textElement.style.color = '#000';
  textElement.textContent = text;
  
  // Posisi teks sedikit di atas marker dan terpusat
  textElement.style.top = '-35px'; // Sesuaikan posisi teks di atas marker
  textElement.style.left = '50%'; // Posisikan teks di tengah secara horizontal
  textElement.style.transform = 'translateX(-50%)'; // Untuk memastikan teks terpusat dengan marker

  return textElement;
}