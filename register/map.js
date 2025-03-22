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
distanceElement.style.backgroundColor = 'rgba(255, 255, 255, 0.5)';
distanceElement.style.padding = '10px';
distanceElement.style.borderRadius = '15px';
distanceElement.style.fontSize = '16px';
distanceElement.style.color = '#ff005b';
distanceElement.style.fontWeight = 'bold';
map.getContainer().appendChild(distanceElement);

// Fungsi untuk mendapatkan rute menggunakan Mapbox Directions API
function getRoute() {
  // Titik asal dan tiga tujuan
  var origin = [110.41124510648847, -6.979465144525227]; // Titik asal
  var destination1 = [110.39930988166765, -6.962930379803198]; // Tujuan pertama
  var destination2 = [110.41124510648847, -6.979465144525227]; // Tujuan akhir

  // Fetch rute pertama (dari asal ke tujuan pertama)
  var url = `https://api.mapbox.com/directions/v5/mapbox/driving/${origin[0]},${origin[1]};${destination1[0]},${destination1[1]}?alternatives=false&geometries=geojson&steps=true&access_token=${mapboxgl.accessToken}`;

  fetch(url)
    .then(response => response.json())
    .then(data => {
      var route1 = data.routes[0].geometry; // Mengambil rute pertama
      var distance1 = data.routes[0].distance / 1000; // Menghitung jarak dalam kilometer

      // Fetch rute kedua (dari tujuan pertama ke tujuan kedua)
      var url2 = `https://api.mapbox.com/directions/v5/mapbox/driving/${destination1[0]},${destination1[1]};${destination2[0]},${destination2[1]}?alternatives=false&geometries=geojson&steps=true&access_token=${mapboxgl.accessToken}`;

      fetch(url2)
        .then(response => response.json())
        .then(data => {
          var route2 = data.routes[0].geometry; // Mengambil rute kedua
          var distance2 = data.routes[0].distance / 1000; // Menghitung jarak dalam kilometer

          // Total jarak
          var totalDistance = distance1 + distance2;
          var distanceText = `Distance ${totalDistance.toFixed(2)} km`; // Format jarak dengan dua angka desimal

          // Update jarak di elemen HTML
          distanceElement.textContent = distanceText;

          // Menambahkan rute pertama sebagai layer pada peta
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
              'line-color': 'rgba(255,0,91, 0.8)', // Warna rute pertama
              'line-width': 4
            }
          });

          // Menambahkan rute kedua sebagai layer pada peta
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
              'line-color': 'rgba(128,128,128, 0.8)', // Warna rute kedua
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

// Panggil fungsi untuk menampilkan lokasi pengguna dan rute saat peta dimuat
map.on('load', function() {
  getRoute();

  // Menambahkan marker kustom dengan gambar PNG untuk 'Start'
  new mapboxgl.Marker({ element: createCustomMarker('../flag.png', 0) }) // Menghapus offset dan rotasi
    .setLngLat([110.41124510648847, -6.979465144525227])
    .addTo(map)
    .getElement().appendChild(createTextElement('START/FINISH')); // Menambahkan teks di atas marker

  // Menambahkan marker kustom dengan gambar PNG untuk 'Checkpoint'
  new mapboxgl.Marker({ element: createCustomMarker('../checkpoint.png', 0) }) // Menghapus offset dan rotasi
    .setLngLat([110.39932636427956, -6.9628614134449744])
    .addTo(map)
    .getElement().appendChild(createTextElement('CHECKPOINT')); // Menambahkan teks di atas marker
})

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