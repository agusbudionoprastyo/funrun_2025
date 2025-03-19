mapboxgl.accessToken = 'pk.eyJ1IjoiYWd1c2J1ZGlvbm9wcmFzdHlvIiwiYSI6ImNsdW1mcTZncTBlc3oyaW1tMGlieHBkMmsifQ.CCttupREZDc0WfusBBm09A';

// Buat peta
var map = new mapboxgl.Map({
  container: 'map', // ID elemen HTML tempat peta akan ditampilkan
  style: 'mapbox://styles/mapbox/streets-v11', // Pilih gaya peta
  center: [110.41201948215935, -6.9794677077623986], // Koordinat awal (longitude, latitude)
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
  var origin = [110.41126589038201, -6.979400345836346]; // Titik asal
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
              'line-color': 'rgba(255,0,91, 0.5)',
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

// Menambahkan lokasi pengguna ke peta
function addUserLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var userLongitude = position.coords.longitude;
      var userLatitude = position.coords.latitude;

      // Membuat elemen div untuk marker
      const circleMarker = document.createElement('div');

      // Mengatur gaya untuk membuat marker berbentuk lingkaran
      circleMarker.style.backgroundColor = 'rgb(235,32,93)'; // Warna marker
      circleMarker.style.width = '15px';           // Lebar marker
      circleMarker.style.height = '15px';          // Tinggi marker
      circleMarker.style.borderRadius = '50%';     // Membuat elemen menjadi lingkaran
      circleMarker.style.cursor = 'pointer';       // Menambahkan kursor pointer saat hover

      // Menambahkan marker ke peta
      new mapboxgl.Marker(circleMarker)
        .setLngLat([userLongitude, userLatitude])  // Set koordinat marker
        .addTo(map);                              // Menambahkan marker ke peta


      // Fungsi untuk memperbarui radius berdasarkan zoom
      function updateRadius() {
        var zoomLevel = map.getZoom();
        var radiusInMeters = 50; // Radius 500 meter yang kita inginkan
        var pixelsPerMeter = Math.pow(2, zoomLevel) * 256 / (40008000 / 360); // Faktor konversi dari meter ke piksel berdasarkan zoom
        var circleRadius = radiusInMeters * pixelsPerMeter / 256;

        if (map.getLayer('user-radius')) {
          map.removeLayer('user-radius');
          map.removeSource('user-radius');
        }

        // Menambahkan radius yang disesuaikan
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
            'circle-radius': circleRadius, // Ukuran radius dalam piksel
            'circle-color': 'rgba(235,32,93, 0.2)' // Warna lingkaran (biru transparan)
          }
        });
      }

      map.on('zoom', updateRadius); // Perbarui radius saat zoom dilakukan
      updateRadius(); // Panggil fungsi pertama kali untuk menampilkan radius

      map.flyTo({ center: [userLongitude, userLatitude], zoom: 15 });
    }, function(error) {
      console.error('Error getting user location:', error);
    });
  } else {
    console.log('Geolocation is not supported by this browser.');
  }
}

// Panggil fungsi untuk mendapatkan rute dan menampilkan lokasi pengguna
map.on('load', function() {
  getRoute();
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