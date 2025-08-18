# Sistem Voucher Code - Lari Sama Mantan 2025

## Overview
Sistem voucher code telah ditambahkan ke form registrasi untuk memberikan potongan harga Rp 15.000 kepada peserta yang menggunakan kode voucher dari komunitas.

## Fitur yang Ditambahkan

### 1. Input Voucher Code
- Field input voucher code menggantikan field email
- Placeholder: "Apply Voucher Code (Optional)"
- Maksimal 20 karakter
- Field bersifat opsional (tidak required)

### 2. Validasi Real-time
- Validasi otomatis saat user mengetik
- Border berubah warna sesuai status:
  - **Abu-abu**: Default state
  - **Hijau**: Voucher valid ✓
  - **Merah**: Voucher tidak valid ✗
- Pesan feedback langsung di bawah input

### 3. Voucher Codes yang Valid
- `KOMUNITAS2025` - Potongan Rp 15.000
- `RUNNING2025` - Potongan Rp 15.000  
- `DAFAM2025` - Potongan Rp 15.000
- `MANTAN2025` - Potongan Rp 15.000

### 4. Perhitungan Otomatis
- Harga otomatis terpotong Rp 15.000 jika voucher valid
- Total amount yang ditampilkan sudah termasuk diskon
- Informasi diskon ditampilkan di modal payment

## Cara Kerja

### Frontend (JavaScript)
1. User memasukkan voucher code
2. Validasi real-time dengan perubahan border dan pesan
3. Data voucher dikirim ke backend saat form submission
4. Informasi diskon disimpan di localStorage
5. Modal payment menampilkan breakdown harga dengan diskon

### Backend (PHP)
1. Menerima voucher code dari form
2. Validasi voucher code di server
3. Hitung diskon Rp 15.000 jika voucher valid
4. Simpan voucher code ke database
5. Return response dengan informasi diskon

## Database Changes

### Tabel `users`
- Kolom baru: `voucher_code` (VARCHAR 50, nullable)

### Tabel `vouchers` (Optional)
- Tabel untuk manajemen voucher yang lebih advanced
- Tracking usage dan expiry date

## File yang Dimodifikasi

1. **`register/index.html`** - UI voucher input
2. **`register/index.js`** - Frontend logic dan validation
3. **`register/register.php`** - Backend processing
4. **`register/assets/css/style.css`** - Styling voucher validation
5. **`add_voucher_code_column.sql`** - Database schema update

## Cara Testing

1. Buka form registrasi
2. Masukkan salah satu voucher code valid (misal: KOMUNITAS2025)
3. Lihat border berubah hijau dan pesan konfirmasi
4. Submit form
5. Di modal payment, lihat breakdown harga dengan diskon

## Keamanan

- Validasi voucher dilakukan di backend (server-side)
- Voucher codes hardcoded untuk sementara
- Bisa dikembangkan dengan database validation
- Rate limiting bisa ditambahkan untuk mencegah abuse

## Pengembangan Selanjutnya

1. **Database-driven vouchers** dengan tabel vouchers
2. **Voucher expiration** dan usage tracking
3. **Admin panel** untuk manajemen voucher
4. **Analytics** penggunaan voucher
5. **Multiple discount tiers** (10%, 20%, dll)
6. **Referral system** yang lebih advanced

## Troubleshooting

### Voucher tidak berfungsi
- Pastikan database sudah diupdate dengan `add_voucher_code_column.sql`
- Check browser console untuk error JavaScript
- Verify voucher code yang dimasukkan (case sensitive)

### Harga tidak terpotong
- Pastikan voucher code valid dan terdaftar
- Check response dari backend di browser network tab
- Verify localStorage menyimpan informasi diskon

## Support

Untuk pertanyaan atau masalah teknis, hubungi tim development.
