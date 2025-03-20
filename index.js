mapboxgl.accessToken = 'pk.eyJ1IjoiYWd1c2J1ZGlvbm9wcmFzdHlvIiwiYSI6ImNsdW1mcTZncTBlc3oyaW1tMGlieHBkMmsifQ.CCttupREZDc0WfusBBm09A';

// Buat peta
var map = new mapboxgl.Map({
  container: 'map', // ID elemen HTML tempat peta akan ditampilkan
  style: 'mapbox://styles/mapbox/streets-v11', // Pilih gaya peta
  center: [110.41126589038201, -6.979400345836346], // Koordinat awal (longitude, latitude)
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
map.getContainer().appendChild(distanceElement);

// Fungsi untuk mendapatkan rute menggunakan Mapbox Directions API
function getRoute() {
  var origin = [110.41126589038201, -6.979400345836346]; // Titik asal menggunakan lokasi pengguna
  var destination1 = [110.39930988166765, -6.962930379803198]; // Tujuan pertama
  var destination2 = [110.41123269023053, -6.979460242162332]; // Tujuan akhir

  var url = `https://api.mapbox.com/directions/v5/mapbox/driving/${origin[0]},${origin[1]};${destination1[0]},${destination1[1]}?alternatives=false&geometries=geojson&steps=true&access_token=${mapboxgl.accessToken}`;

  fetch(url)
    .then(response => response.json())
    .then(data => {
      var route1 = data.routes[0].geometry;
      var distance1 = data.routes[0].distance / 1000;

      var url2 = `https://api.mapbox.com/directions/v5/mapbox/driving/${destination1[0]},${destination1[1]};${destination2[0]},${destination2[1]}?alternatives=false&geometries=geojson&steps=true&access_token=${mapboxgl.accessToken}`;

      fetch(url2)
        .then(response => response.json())
        .then(data => {
          var route2 = data.routes[0].geometry;
          var distance2 = data.routes[0].distance / 1000;

          var totalDistance = distance1 + distance2;
          var distanceText = `Distance ${totalDistance.toFixed(2)} km`;

          distanceElement.textContent = distanceText;

          map.addLayer({
            'id': 'route1',
            'type': 'line',
            'source': {
              'type': 'geojson',
              'data': {
                'type': 'Feature',
                'properties': {},
                'geometry': route1
              }
            },
            'paint': {
              'line-color': 'rgba(255,0,91, 0.8)',
              'line-width': 4
            }
          });

          map.addLayer({
            'id': 'route2',
            'type': 'line',
            'source': {
              'type': 'geojson',
              'data': {
                'type': 'Feature',
                'properties': {},
                'geometry': route2
              }
            },
            'paint': {
              'line-color': 'rgba(128,128,128, 0.8)',
              'line-width': 4
            }
          });
        })
        .catch(error => {
          console.error('Error fetching route from destination 1 to destination 2:', error);
        });
    })
    .catch(error => {
      console.error('Error fetching route from origin to destination 1:', error);
    });
}

// Menambahkan lokasi pengguna ke peta secara real-time
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
      circleMarker.style.width = '15px';
      circleMarker.style.height = '15px';
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

        if (map.getLayer('user-radius')) {
          map.removeLayer('user-radius');
          map.removeSource('user-radius');
        }

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

      map.on('zoom', updateRadius); // Perbarui radius saat zoom dilakukan
      updateRadius(); // Panggil fungsi pertama kali untuk menampilkan radius

      map.flyTo({ center: [userLongitude, userLatitude], zoom: 15 });

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

// Tambahkan tombol locate GPS
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
      circleMarker.style.width = '15px';
      circleMarker.style.height = '15px';
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

  new mapboxgl.Marker({ color: 'pink' })
    .setLngLat([110.41126589038201, -6.979400345836346])
    .setPopup(new mapboxgl.Popup().setText('Start'))
    .addTo(map);

  new mapboxgl.Marker({ color: 'teal' })
    .setLngLat([110.39930988166765, -6.962930379803198])
    .setPopup(new mapboxgl.Popup().setText('Checkpoint'))
    .addTo(map);

  new mapboxgl.Marker({ color: 'gray' })
    .setLngLat([110.41123269023053, -6.979460242162332])
    .setPopup(new mapboxgl.Popup().setText('Finish'))
    .addTo(map);
});
