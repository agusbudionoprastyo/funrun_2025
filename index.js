mapboxgl.accessToken = 'pk.eyJ1IjoiYWd1c2J1ZGlvbm9wcmFzdHlvIiwiYSI6ImNsdW1mcTZncTBlc3oyaW1tMGlieHBkMmsifQ.CCttupREZDc0WfusBBm09A';

// Buat peta
var map = new mapboxgl.Map({
  container: 'map', // ID elemen HTML tempat peta akan ditampilkan
  style: 'mapbox://styles/mapbox/streets-v11', // Pilih gaya peta
  center: [110.41173454329623, -6.978404646181601], // Koordinat awal (longitude, latitude) - Point A
  zoom: 15
});

// Buat kontrol peta (zoom, compass)
map.addControl(new mapboxgl.NavigationControl());

// Menambahkan elemen untuk menampilkan jarak di atas peta
var distanceElement = document.createElement('div');
distanceElement.className = 'distance-info';
distanceElement.style.position = 'absolute';
distanceElement.style.top = '10px';
distanceElement.style.left = '4px';
distanceElement.style.backgroundColor = 'rgba(255, 255, 255, 0.5)';
distanceElement.style.padding = '10px';
distanceElement.style.borderRadius = '10px';
distanceElement.style.fontSize = '16px';
distanceElement.style.color = '#000000';
distanceElement.style.fontWeight = 'bold';
// Isi awal distanceElement
// Akan diupdate oleh displayCompleteRoute()
distanceElement.innerHTML = 'Funrun 8K<br><span style="display:block; font-size:14px; font-weight:normal; color:#222;">ⓘ Marshal</span>';
map.getContainer().appendChild(distanceElement);

// Definisi semua checkpoint
var checkpoints = [
  { id: 'A', lng: 110.41123333286674, lat: -6.979436806518359, name: 'START/FINISH' },
  { id: 'B', lng: 110.4132822844655, lat: -6.9749235700715015, name: 'Marshal 1' },
  { id: 'C', lng: 110.41480694499647, lat: -6.973531583072864, name: 'Marshal 2' },
  { id: 'D', lng: 110.41953248369022, lat: -6.971251919716325, name: 'Marshal 3' },
  { id: 'E', lng: 110.42232579371996, lat: -6.969924453250012, name: 'Marshal 4'},
  { id: 'F', lng: 110.42517418618024, lat: -6.9685237492191305, name: 'Marshal 5' },
  { id: 'G', lng: 110.42511580431477, lat: -6.966584677114943, name: 'Marshal 6' },
  { id: 'H', lng: 110.42925817984032, lat: -6.965291861701476, name: 'Marshal 7' },
  { id: 'I', lng: 110.43008569448317, lat: -6.967665290748463, name: 'Marshal 8' },
  { id: 'J', lng: 110.4252425562517, lat: -6.968728790584302, name: 'Marshal 9' },
  { id: 'K', lng: 110.4228079211141, lat: -6.971106525390175, name: 'Marshal 10' },
  { id: 'L', lng: 110.42013374224155, lat: -6.973821451482721, name: 'Marshal 11' },
  { id: 'M', lng: 110.41674208343511, lat: -6.977250726160813, name: 'Marshal 12' },
  { id: 'N', lng: 110.41565448478823, lat: -6.978286651448628, name: 'Marshal 13' },
  { id: 'O', lng: 110.40971114259015, lat: -6.983524400093781, name: 'Marshal 14' },
  { id: 'Q', lng: 110.42388938448875, lat: -6.969628740201411, name: 'Water Station' },
  { id: 'R', lng: 110.4183192790272, lat: -6.975631995264069, name: 'Water Station' }
];

// Definisi checkpoint yang menjadi bagian rute utama
var routeCheckpoints = [
  { id: 'A', lng: 110.41123333286674, lat: -6.979436806518359, name: 'START/FINISH' },
  { id: 'B', lng: 110.4132822844655, lat: -6.9749235700715015, name: 'ⓘ' },
  { id: 'C', lng: 110.41480694499647, lat: -6.973531583072864, name: 'ⓘ' },
  { id: 'D', lng: 110.41953248369022, lat: -6.971251919716325, name: 'ⓘ' },
  { id: 'E', lng: 110.42232579371996, lat: -6.969924453250012, name: 'ⓘ'},
  { id: 'F', lng: 110.42517418618024, lat: -6.9685237492191305, name: 'ⓘ' },
  { id: 'G', lng: 110.42511580431477, lat: -6.966584677114943, name: 'ⓘ' },
  { id: 'H', lng: 110.42925817984032, lat: -6.965291861701476, name: 'ⓘ' },
  { id: 'I', lng: 110.43008569448317, lat: -6.967665290748463, name: 'ⓘ' },
  { id: 'J', lng: 110.4252425562517, lat: -6.968728790584302, name: 'ⓘ' },
  { id: 'K', lng: 110.4228079211141, lat: -6.971106525390175, name: 'ⓘ' },
  { id: 'L', lng: 110.42013374224155, lat: -6.973821451482721, name: 'ⓘ' },
  { id: 'M', lng: 110.41674208343511, lat: -6.977250726160813, name: 'ⓘ' },
  { id: 'N', lng: 110.41565448478823, lat: -6.978286651448628, name: 'ⓘ' },
  { id: 'O', lng: 110.40971114259015, lat: -6.983524400093781, name: 'ⓘ' },
  { id: 'A', lng: 110.41123333286674, lat: -6.979436806518359, name: 'START/FINISH' }
];

// Fungsi untuk mendapatkan rute menggunakan Mapbox Directions API
function getRoute() {
  // Bagi checkpoint menjadi segment yang lebih kecil (maksimal 5 waypoint per segment)
  var segments = [];
  for (let i = 0; i < routeCheckpoints.length - 1; i += 4) {
    var segment = routeCheckpoints.slice(i, Math.min(i + 5, routeCheckpoints.length));
    segments.push(segment);
  }
  
  var totalDistance = 0;
  var allRouteCoordinates = [];
  var completedSegments = 0;
  
  function processSegment(segmentIndex) {
    if (segmentIndex >= segments.length) {
      // Semua segment selesai, tampilkan rute lengkap
      // Tambahkan layer rute baru
      // Hapus layer rute yang lama jika ada
      if (map.getLayer('route')) {
        map.removeLayer('route');
      }
      if (map.getSource('route')) {
        map.removeSource('route');
      }
      map.addLayer({
        'id': 'route',
        'type': 'line',
        'source': {
          'type': 'geojson',
          'data': {
            'type': 'Feature',
            'properties': {},
            'geometry': {
              'type': 'LineString',
              'coordinates': allRouteCoordinates
            }
          }
        },
        'paint': {
          'line-color': 'rgba(255,0,91, 0.8)',
          'line-width': 4
        }
      });
      console.log('Complete route displayed with', allRouteCoordinates.length, 'coordinates');
      return;
    }
    
    var segment = segments[segmentIndex];
    var coordinates = segment.map(cp => `${cp.lng},${cp.lat}`).join(';');
    
    var url = `https://api.mapbox.com/directions/v5/mapbox/driving/${coordinates}?alternatives=false&geometries=geojson&steps=true&access_token=${mapboxgl.accessToken}`;
    
    fetch(url)
      .then(response => response.json())
      .then(data => {
        console.log(`Segment ${segmentIndex + 1} data:`, data);
        
        if (data.routes && data.routes.length > 0) {
          var route = data.routes[0];
          var segmentDistance = route.distance / 1000;
          totalDistance += segmentDistance;
          
          // Tambahkan koordinat rute segment ini ke array utama
          var segmentCoords = route.geometry.coordinates;
          allRouteCoordinates = allRouteCoordinates.concat(segmentCoords);
          
          completedSegments++;
          
          // Lanjut ke segment berikutnya
          processSegment(segmentIndex + 1);
        } else {
          console.error(`No routes found for segment ${segmentIndex + 1}`);
          processSegment(segmentIndex + 1);
        }
      })
      .catch(error => {
        console.error(`Error fetching segment ${segmentIndex + 1}:`, error);
        processSegment(segmentIndex + 1);
      });
  }
  
  // Mulai dengan segment pertama
  processSegment(0);
}

// Fungsi untuk menambahkan semua checkpoint markers
function addCheckpointMarkers() {
  checkpoints.forEach((checkpoint, index) => {
    var markerElement = createTextMarker(checkpoint.name);
    var marker = new mapboxgl.Marker({ element: markerElement })
      .setLngLat([checkpoint.lng, checkpoint.lat])
      .addTo(map);

    // Marker yang bisa popup: nama mengandung 'Marshal', atau persis 'ⓘ' atau 'Water Station'
    if (
      (checkpoint.name && checkpoint.name.toLowerCase().includes('marshal')) ||
      checkpoint.name === 'ⓘ' ||
      checkpoint.name === 'Water Station'
    ) {
      marker.getElement().addEventListener('click', function() {
        // Fetch alamat dari Mapbox Geocoding API
        var geocodeUrl = `https://api.mapbox.com/geocoding/v5/mapbox.places/${checkpoint.lng},${checkpoint.lat}.json?access_token=${mapboxgl.accessToken}`;
        fetch(geocodeUrl)
          .then(response => response.json())
          .then(data => {
            var address = 'Alamat tidak ditemukan';
            if (data.features && data.features.length > 0) {
              address = data.features[0].place_name;
            }
            // Tampilkan popup di marker
            new mapboxgl.Popup()
              .setLngLat([checkpoint.lng, checkpoint.lat])
              .setHTML(`<strong>${checkpoint.name}</strong><br>${address}`)
              .addTo(map);
          })
          .catch(() => {
            new mapboxgl.Popup()
              .setLngLat([checkpoint.lng, checkpoint.lat])
              .setHTML(`<strong>${checkpoint.name}</strong><br>Alamat tidak ditemukan`)
              .addTo(map);
          });
      });
    }
  });
}

function addUserLocation() {
  if (navigator.geolocation) {
    // Watch posisi pengguna untuk update secara real-time
    navigator.geolocation.watchPosition(function(position) {
      var userLongitude = position.coords.longitude;
      var userLatitude = position.coords.latitude;

      // Hapus marker yang lama jika sudah ada
      if (window.userMarker) {
        window.userMarker.remove();
      }

      // Menambahkan marker untuk lokasi pengguna
      const circleMarker = document.createElement('div');
      circleMarker.style.backgroundColor = 'rgb(235,32,93)';
      circleMarker.style.width = '20px';
      circleMarker.style.height = '20px';
      circleMarker.style.border = '3px solid white';  // Menambahkan border putih
      circleMarker.style.borderRadius = '50%';
      circleMarker.style.cursor = 'pointer';

      window.userMarker = new mapboxgl.Marker(circleMarker)
        .setLngLat([userLongitude, userLatitude])
        .addTo(map);

      // Fungsi untuk memperbarui radius berdasarkan zoom
      function updateRadius() {
        var zoomLevel = map.getZoom();
        var radiusInMeters = 50; // Radius 50 meter
        var pixelsPerMeter = Math.pow(2, zoomLevel) * 256 / (40008000 / 360);
        var circleRadius = radiusInMeters * pixelsPerMeter / 256;

        // Periksa jika layer sudah ada, dan hanya update layer yang ada
        if (map.getLayer('user-radius')) {
          map.getSource('user-radius').setData({
            'type': 'Feature',
            'geometry': {
              'type': 'Point',
              'coordinates': [userLongitude, userLatitude]
            }
          });

          // Update circle-radius jika layer sudah ada
          map.setPaintProperty('user-radius', 'circle-radius', circleRadius);
        } else {
          // Jika layer belum ada, buat layer baru
          map.addLayer({
            'id': 'user-radius',
            'type': 'circle',
            'source': {
              'type': 'geojson',
              'data': {
                'type': 'Feature',
                'geometry': {
                  'type': 'Point',
                  'coordinates': [userLongitude, userLatitude]
                }
              }
            },
            'paint': {
              'circle-radius': circleRadius,
              'circle-color': 'rgba(235,32,93, 0.2)'
            }
          });
        }
      }

      // Menambahkan listener untuk zoom, tanpa perlu menghapus dan menambah layer baru
      map.on('zoom', updateRadius); // Perbarui radius saat zoom dilakukan
      updateRadius(); // Panggil fungsi pertama kali untuk menampilkan radius

      // Panggil fungsi untuk mendapatkan rute dengan lokasi pengguna
      getRoute();
    }, function(error) {
      console.error('Error getting user location:', error);
    }, {
      enableHighAccuracy: true,
      maximumAge: 10000, // Update lokasi setiap 10 detik
      timeout: 5000 // Timeout setelah 5 detik jika tidak dapat mendapatkan lokasi
    });
  } else {
    console.log('Geolocation is not supported by this browser.');
  }
}


var locateButton = document.createElement('button');
    locateButton.id = 'locate-btn';
    locateButton.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i>';
    locateButton.style.position = 'absolute';
    locateButton.style.bottom = '50px';
    locateButton.style.right = '10px';
    locateButton.style.width = '50px'; // Ukuran tombol lebih besar untuk memberi ruang pada ikon
    locateButton.style.height = '50px'; // Ukuran tombol lebih besar untuk memberi ruang pada ikon
    locateButton.style.zIndex = 10;
    locateButton.style.padding = '0'; // Hapus padding agar ikon tepat di tengah
    locateButton.style.backgroundColor = 'rgba(255, 255, 255, 0.8)'; // Warna latar belakang dengan sedikit transparansi
    locateButton.style.color = 'black';
    locateButton.style.border = 'none';
    locateButton.style.borderRadius = '50%'; // Membuat tombol berbentuk lingkaran
    locateButton.style.cursor = 'pointer';

// Styling untuk ikon agar berada di tengah
var icon = locateButton.querySelector('i');
icon.style.fontSize = '20px'; // Ukuran ikon
icon.style.lineHeight = '50px'; // Pastikan ikon terpusat secara vertikal

document.body.appendChild(locateButton);

// Event listener untuk tombol locate GPS
locateButton.addEventListener('click', function() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var userLongitude = position.coords.longitude;
      var userLatitude = position.coords.latitude;

      // Pindahkan peta ke lokasi pengguna
      map.flyTo({
        center: [userLongitude, userLatitude],
        zoom: 15
      });

      // Hapus marker lama jika ada
      if (window.userMarker) {
        window.userMarker.remove();
      }

      const circleMarker = document.createElement('div');
      circleMarker.style.backgroundColor = 'rgb(235,32,93)';
      circleMarker.style.width = '20px';
      circleMarker.style.height = '20px';
      circleMarker.style.border = '3px solid white';
      circleMarker.style.borderRadius = '50%';
      circleMarker.style.cursor = 'pointer';

      window.userMarker = new mapboxgl.Marker(circleMarker)
        .setLngLat([userLongitude, userLatitude])
        .addTo(map);

      updateRadius(userLongitude, userLatitude);
    }, function(error) {
      console.error('Error getting user location:', error);
    }, {
      enableHighAccuracy: true,
      maximumAge: 10000, // Update lokasi setiap 10 detik
      timeout: 5000 // Timeout setelah 5 detik jika tidak dapat mendapatkan lokasi
    });
  } else {
    console.log('Geolocation is not supported by this browser.');
  }
});

// Panggil fungsi untuk menampilkan lokasi pengguna dan rute saat peta dimuat
map.on('load', function() {
  addUserLocation();
  
  // Menambahkan semua checkpoint markers
  addCheckpointMarkers();
  
  // Panggil fungsi untuk mendapatkan rute
  getRoute();
})

// Fungsi untuk membuat marker dengan teks
function createTextMarker(text) {
  const markerDiv = document.createElement('div');
  
  // Khusus untuk START dan FINISH, gunakan background persegi panjang
  if (text === 'START/FINISH') {
    // Marker tanpa background, dengan emoji 🏁 di atas label
    markerDiv.style.display = 'flex';
    markerDiv.style.flexDirection = 'column';
    markerDiv.style.alignItems = 'center';
    markerDiv.style.justifyContent = 'center';
    markerDiv.style.background = 'none';
    markerDiv.style.border = 'none';
    markerDiv.style.boxShadow = 'none';
    markerDiv.style.padding = '0';

    // Tambahkan emoji flag
    const flag = document.createElement('span');
    flag.textContent = '🏁';
    flag.style.fontSize = '28px';
    flag.style.lineHeight = '1';
    flag.style.marginBottom = '-4px';
    markerDiv.appendChild(flag);

    // Tambahkan label
    const label = document.createElement('span');
    label.textContent = text;
    label.style.fontWeight = 'bold';
    label.style.fontSize = '13px';
    label.style.color = 'black';
    label.style.background = 'none';
    label.style.border = 'none';
    label.style.marginTop = '0px';
    markerDiv.appendChild(label);
  } else if (text === 'ⓘ' || (text && text.toLowerCase().includes('marshal'))) {
    // Untuk simbol info (ⓘ) atau Marshal, buat marker kecil bulat putih dengan isi ⓘ
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
  } else if (text === 'WS') {
    // Untuk WS (Water Station), buat marker dengan background biru muda
    markerDiv.style.width = 'auto';
    markerDiv.style.minWidth = '40px';
    markerDiv.style.height = '25px';
    markerDiv.style.backgroundColor = 'rgba(255, 255, 255, 0.9)';
    markerDiv.style.border = '2px solid white';
    markerDiv.style.borderRadius = '12px';
    markerDiv.style.display = 'flex';
    markerDiv.style.alignItems = 'center';
    markerDiv.style.justifyContent = 'center';
    markerDiv.style.fontWeight = 'bold';
    markerDiv.style.fontSize = '10px';
    markerDiv.style.color = 'black';
    markerDiv.style.cursor = 'pointer';
    markerDiv.style.boxShadow = '0 1px 3px rgba(0,0,0,0.3)';
    markerDiv.style.padding = '0 6px';
    markerDiv.textContent = text;
  } else if (text === 'Water Station') {
    // Untuk Water Station, buat marker dengan background biru muda
    markerDiv.style.width = 'auto';
    markerDiv.style.minWidth = '80px';
    markerDiv.style.height = '25px';
    markerDiv.style.backgroundColor = 'rgba(255, 255, 255, 0.9)';
    markerDiv.style.border = '2px solid white';
    markerDiv.style.borderRadius = '12px';
    markerDiv.style.display = 'flex';
    markerDiv.style.alignItems = 'center';
    markerDiv.style.justifyContent = 'center';
    markerDiv.style.fontWeight = 'bold';
    markerDiv.style.fontSize = '10px';
    markerDiv.style.color = 'black';
    markerDiv.style.cursor = 'pointer';
    markerDiv.style.boxShadow = '0 1px 3px rgba(0,0,0,0.3)';
    markerDiv.style.padding = '0 6px';
    markerDiv.textContent = text;
  } else {
    // Untuk checkpoint lain tetap menggunakan bentuk bulat
    markerDiv.style.width = '30px';
    markerDiv.style.height = '30px';
    markerDiv.style.backgroundColor = 'rgba(255, 255, 255, 0.9)';
    // markerDiv.style.border = '3px solid #ff5b1c';
    markerDiv.style.borderRadius = '50%';
    markerDiv.style.display = 'flex';
    markerDiv.style.alignItems = 'center';
    markerDiv.style.justifyContent = 'center';
    markerDiv.style.fontWeight = 'bold';
    markerDiv.style.fontSize = '14px';
    markerDiv.style.color = '#ff5b1c';
    markerDiv.style.cursor = 'pointer';
    markerDiv.style.boxShadow = '0 2px 4px rgba(0,0,0,0.3)';
    markerDiv.textContent = text;
  }
  
  return markerDiv;
}