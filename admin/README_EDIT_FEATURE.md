# Fitur Edit Inline untuk Admin Panel

## Deskripsi
Fitur ini memungkinkan admin untuk mengedit warna jersey dan status pembayaran langsung dari tabel tanpa perlu membuka halaman terpisah.

## Fitur yang Ditambahkan

### 1. Edit Warna Jersey
- Klik pada warna jersey untuk mengedit
- Dropdown dengan pilihan warna: darkblue, purple, red, green, yellow, orange, pink, black, white
- Update otomatis ke database
- Notifikasi sukses/error menggunakan IziToast

### 2. Edit Status Pembayaran
- Klik pada status pembayaran untuk mengedit
- Dropdown dengan pilihan: pending, paid, verified
- Update otomatis ke database
- Badge class berubah sesuai status baru
- Notifikasi sukses/error menggunakan IziToast

## File yang Dimodifikasi

### 1. `admin/index.php`
- Menambahkan class `editable-jersey` dan `editable-status`
- Menambahkan event listener untuk handle click
- Menambahkan CSS styling untuk hover effect
- Menambahkan tooltip untuk user guidance

### 2. `admin/update_jersey_status.php` (Baru)
- Endpoint API untuk update jersey color dan status
- Validasi input dan field
- Error handling dan response JSON
- Support untuk update jersey color per user (user 1 atau user 2)

### 3. `admin/test_edit.html` (Baru)
- File test untuk memverifikasi fitur edit inline
- Dapat digunakan untuk testing tanpa database

## Cara Penggunaan

### Edit Warna Jersey
1. Klik pada warna jersey di kolom "Jersey Color"
2. Dropdown akan muncul dengan pilihan warna
3. Pilih warna baru
4. Data akan otomatis tersimpan ke database
5. Notifikasi sukses akan muncul

### Edit Status Pembayaran
1. Klik pada status di kolom "Payment Status"
2. Dropdown akan muncul dengan pilihan status
3. Pilih status baru
4. Data akan otomatis tersimpan ke database
5. Badge akan berubah warna sesuai status baru
6. Notifikasi sukses akan muncul

## Styling

### CSS Classes
- `.editable-jersey`: Untuk elemen warna jersey yang dapat diedit
- `.editable-status`: Untuk elemen status yang dapat diedit
- `.editable-dropdown`: Untuk styling dropdown saat edit

### Hover Effects
- Scale transform saat hover
- Background color change
- Smooth transition animation

## Error Handling

### Database Errors
- Validasi field yang diizinkan
- Error message yang informatif
- Rollback jika terjadi error

### UI Errors
- Notifikasi error menggunakan IziToast
- Fallback jika request gagal
- Cancel edit jika user blur dari dropdown

## Security

### Input Validation
- Validasi field yang diizinkan
- Sanitasi input
- Prepared statements untuk mencegah SQL injection

### Access Control
- Session validation
- Admin-only access

## Testing

### Manual Testing
1. Buka `admin/test_edit.html` untuk test UI
2. Test di `admin/index.php` dengan data real
3. Verifikasi update di database

### Debugging
- Check browser console untuk JavaScript errors
- Check PHP error log untuk backend errors
- Use browser network tab untuk API calls

## Troubleshooting

### Common Issues
1. **Dropdown tidak muncul**: Pastikan JavaScript tidak error
2. **Update gagal**: Check database connection dan query
3. **Styling tidak konsisten**: Pastikan CSS classes ter-load dengan benar

### Debug Steps
1. Open browser developer tools
2. Check console for errors
3. Check network tab for failed requests
4. Verify database connection
5. Check PHP error logs 