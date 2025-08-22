# Member Referral System - Lari Sama Mantan 2025

## Overview
Sistem tracking member referral dengan dynamic links yang memungkinkan setiap komunitas memiliki link registrasi khusus untuk menandai member yang register melalui mereka.

## Fitur Utama

### 1. Dynamic Member Referral Links
- **URL Format**: `register/index.html?member=KODE_KOMUNITAS`
- **Tracking**: Menandai member yang register melalui komunitas tertentu
- **Visual Indicator**: Tampilan "Member Registration - Referred by: KODE" di form
- **No Discount**: Tidak ada potongan harga, hanya untuk tracking

### 2. Member Codes yang Tersedia
- `SEMARANGRUNNER` - Semarang Runner Community
- `FAKERUNNER` - Fake Runner Community  
- `BERLARIBERSAMA` - Berlari Bersama Community
- `PLAYONAMBYAR` - Playon Ambyar Community
- `PLAYONNDESO` - Playon Ndeso Community
- `BESTIFITY` - Bestifity Community
- `DURAKINGRUN` - Duraking Run Community
- `SALATIGARB` - Salatiga Running Community
- `PELARIAN` - Pelarian Community

### 3. Link Generator Tool
- **File**: `register/generate_member_links.php`
- **Fitur**: Generate semua link member referral dengan satu klik
- **QR Code**: Generate QR code untuk setiap link
- **Copy Link**: Copy link ke clipboard dengan mudah

### 4. Member Tracking System
- **Database**: Tabel `member_registrations` untuk tracking
- **Statistics**: Dashboard statistik member referrals
- **Analytics**: Chart dan grafik penggunaan per komunitas

## Cara Penggunaan

### Untuk Admin/Organizer

#### 1. Generate Member Links
```
Akses: register/generate_member_links.php
```
- Lihat semua member codes yang tersedia
- Copy link untuk setiap komunitas
- Generate QR code untuk sharing
- Test link langsung dari dashboard

#### 2. Monitor Statistics
```
Akses: admin/member_stats.php
```
- Dashboard statistik member referrals
- Chart penggunaan per komunitas
- Recent referrals tracking
- Total users per komunitas

### Untuk Member/Komunitas

#### 1. Share Link
Setiap komunitas mendapat link khusus:
```
https://yourdomain.com/register/index.html?member=SEMARANGRUNNER
https://yourdomain.com/register/index.html?member=FAKERUNNER
https://yourdomain.com/register/index.html?member=PELARIAN
```

#### 2. User Experience
- User klik link komunitas
- Indikator "Member Registration - Referred by: KODE" muncul
- Proses registrasi normal (tanpa potongan harga)
- Tracking otomatis tercatat di database

## Database Schema

### Tabel `member_registrations` (Baru)
```sql
CREATE TABLE member_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_code VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(100) NOT NULL,
    registration_type ENUM('single', 'couple') NOT NULL,
    user_count INT DEFAULT 1,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_member_code (member_code),
    INDEX idx_transaction_id (transaction_id),
    INDEX idx_registered_at (registered_at)
);
```

### View `member_stats` (Baru)
```sql
CREATE VIEW member_stats AS
SELECT 
    member_code,
    COUNT(id) as total_registrations,
    SUM(user_count) as total_users_registered,
    MIN(registered_at) as first_registration,
    MAX(registered_at) as last_registration
FROM member_registrations
GROUP BY member_code
ORDER BY total_users_registered DESC;
```

## File yang Ditambahkan/Dimodifikasi

### File Baru
1. **`register/generate_member_links.php`** - Link generator tool
2. **`register/track_member_registration.php`** - API tracking member
3. **`admin/member_stats.php`** - Dashboard statistik
4. **`add_member_tracking_table.sql`** - Database schema
5. **`MEMBER_REGISTRATION_SYSTEM.md`** - Dokumentasi ini

### File Dimodifikasi
1. **`register/index.js`** - URL parameter handling & member tracking

## Setup Instructions

### 1. Database Setup
```bash
# Jalankan SQL files
mysql -u username -p database_name < add_member_tracking_table.sql
```

### 2. File Permissions
```bash
# Pastikan file PHP bisa diakses
chmod 644 register/generate_member_links.php
chmod 644 register/track_member_registration.php
chmod 644 admin/member_stats.php
```

### 3. Testing
1. Buka `register/generate_member_links.php`
2. Copy salah satu link (misal: SEMARANGRUNNER)
3. Buka link di browser baru
4. Verifikasi indikator member registration muncul
5. Verifikasi tracking tercatat di database

## Contoh Link Member

### Semarang Runner
```
https://yourdomain.com/register/index.html?member=SEMARANGRUNNER
```

### Fake Runner
```
https://yourdomain.com/register/index.html?member=FAKERUNNER
```

### Pelarian
```
https://yourdomain.com/register/index.html?member=PELARIAN
```

## Keuntungan Sistem

### 1. Tracking yang Akurat
- Setiap registrasi member tercatat dengan komunitas referral
- Statistik penggunaan per komunitas
- Analytics untuk evaluasi campaign

### 2. User Experience yang Baik
- Tidak mengganggu proses registrasi normal
- Visual feedback yang jelas
- Tidak ada potongan harga yang membingungkan

### 3. Kemudahan Management
- Generate link dengan satu klik
- QR code untuk sharing mudah
- Dashboard monitoring real-time

### 4. Scalability
- Mudah tambah komunitas baru
- Sistem tracking yang robust
- Database yang terstruktur

## Troubleshooting

### Indikator member tidak muncul
- Pastikan URL parameter `?member=KODE` benar
- Check browser console untuk error JavaScript
- Verifikasi member code ada di daftar

### Link generator tidak muncul
- Pastikan database connection berfungsi
- Check file permissions
- Verifikasi tabel `member_registrations` sudah ada

### Tracking tidak berfungsi
- Pastikan tabel `member_registrations` sudah dibuat
- Check API endpoint `track_member_registration.php`
- Verifikasi JavaScript tracking berjalan

## Pengembangan Selanjutnya

1. **Email Notifications** - Notifikasi ke admin saat member register
2. **SMS Notifications** - SMS ke member setelah registrasi
3. **Social Media Integration** - Share link ke social media
4. **Advanced Analytics** - Chart yang lebih detail
5. **Bulk Link Generation** - Generate multiple links sekaligus
6. **Custom Landing Pages** - Landing page khusus per komunitas
7. **Leaderboard System** - Ranking komunitas berdasarkan referrals
8. **Reward System** - Reward untuk komunitas dengan referrals terbanyak
