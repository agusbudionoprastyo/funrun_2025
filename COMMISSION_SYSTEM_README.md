# Commission System Implementation

## Overview
Sistem komisi referral yang memungkinkan setiap referrer memiliki rate komisi yang berbeda dan tracking komisi yang lengkap.

## Features

### 1. Commission Rates per Referrer
- **ag**: 5% (Rp 5.000)
- **admin**: 3% (Rp 3.000)
- **dafam**: 4% (Rp 4.000)
- **runner**: 2.5% (Rp 2.500)
- **bluehouse**: 10% (Rp 10.000)

### 2. Database Structure

#### `referrer_codes` table (updated):
```sql
- commission_rate DECIMAL(5,2) - Rate komisi dalam persen
- commission_amount DECIMAL(10,2) - Jumlah komisi tetap
- total_commission DECIMAL(10,2) - Total komisi yang sudah dikumpulkan
- referral_link VARCHAR(255) - Link referral otomatis
```

#### `referrals` table (updated):
```sql
- commission_amount DECIMAL(10,2) - Jumlah komisi untuk referral ini
- commission_paid BOOLEAN - Status pembayaran komisi
- commission_paid_date TIMESTAMP - Tanggal pembayaran komisi
```

#### `commission_transactions` table (new):
```sql
- id INT AUTO_INCREMENT PRIMARY KEY
- referrer_code VARCHAR(50) - Kode referrer
- referral_id INT - ID referral
- transaction_id VARCHAR(100) - ID transaksi
- commission_amount DECIMAL(10,2) - Jumlah komisi
- commission_rate DECIMAL(5,2) - Rate komisi
- base_amount DECIMAL(10,2) - Jumlah dasar transaksi
- status ENUM('pending', 'paid', 'cancelled') - Status komisi
- created_at TIMESTAMP - Tanggal dibuat
- paid_at TIMESTAMP - Tanggal dibayar
```

### 3. Admin Dashboard Features

#### Referral Management Page:
- **Total Commission**: Total komisi dari semua referrer
- **Pending Commission**: Komisi yang belum dibayar
- **Commission Rate**: Rate komisi setiap referrer
- **Referral Link**: Link otomatis untuk setiap referrer
- **Details Button**: Lihat detail lengkap setiap referrer

#### Referrer Details Page:
- **Basic Info**: Kode, rate komisi, jumlah komisi
- **Statistics**: Total komisi, status aktif, tanggal dibuat
- **Referral Link**: Link yang bisa di-copy
- **Referral History**: Riwayat semua referral dengan komisi

## Implementation Details

### Files Created/Modified:

#### New Files:
- `add_commission_system.sql` - Database setup untuk sistem komisi
- `setup_commission_system.php` - Script setup lengkap
- `update_existing_commissions.php` - Update referral yang sudah ada
- `admin/referrer_details.php` - Halaman detail referrer
- `COMMISSION_SYSTEM_README.md` - Dokumentasi ini

#### Modified Files:
- `register/process_referral.php` - Menambahkan perhitungan komisi
- `admin/referral_stats.php` - Menambahkan data komisi
- `admin/referral_management.php` - UI untuk sistem komisi
- `admin/add_referrer.php` - Form untuk menambah referrer dengan komisi

### How It Works:

1. **Referral Processing**: Saat ada registrasi baru, sistem otomatis menghitung komisi berdasarkan rate referrer
2. **Commission Tracking**: Setiap komisi dicatat di `commission_transactions` table
3. **Total Calculation**: Total komisi diupdate otomatis di `referrer_codes` table
4. **Admin Dashboard**: Menampilkan semua data komisi dengan format currency Indonesia

### Commission Calculation:

```php
// Contoh perhitungan komisi
$baseAmount = 100000; // Harga registrasi
$commissionRate = 5.00; // 5%
$commissionAmount = 5000; // Rp 5.000 (fixed amount)

// Sistem menggunakan fixed amount, bukan percentage
// Untuk fleksibilitas lebih baik
```

## Installation Steps

### 1. Setup Database:
```bash
# Jalankan script setup
https://funrun.dafam.cloud/setup_commission_system.php
```

### 2. Update Existing Data:
```bash
# Update referral yang sudah ada
https://funrun.dafam.cloud/update_existing_commissions.php
```

### 3. Test System:
```bash
# Test referral dengan komisi
https://funrun.dafam.cloud/register/?member=bluehouse
```

## Usage Examples

### For Referrers:
- **Bluehouse**: `https://funrun.dafam.cloud/register/?member=bluehouse` (Rp 10.000 per referral)
- **AG**: `https://funrun.dafam.cloud/register/?member=ag` (Rp 5.000 per referral)
- **Dafam**: `https://funrun.dafam.cloud/register/?member=dafam` (Rp 4.000 per referral)

### For Administrators:
- **View Management**: `/admin/referral_management.php`
- **View Details**: `/admin/referrer_details.php?code=bluehouse`
- **Add New Referrer**: Form di halaman management

## Commission Rates Summary

| Referrer Code | Name | Commission Rate | Commission Amount | Link |
|---------------|------|-----------------|-------------------|------|
| ag | Admin User | 5% | Rp 5.000 | `?member=ag` |
| admin | Administrator | 3% | Rp 3.000 | `?member=admin` |
| dafam | Dafam Team | 4% | Rp 4.000 | `?member=dafam` |
| runner | Runner Community | 2.5% | Rp 2.500 | `?member=runner` |
| bluehouse | Blue House Team | 10% | Rp 10.000 | `?member=bluehouse` |

## Benefits

- **Flexible Commission**: Setiap referrer bisa punya rate berbeda
- **Automatic Tracking**: Komisi dihitung dan dicatat otomatis
- **Complete History**: Riwayat lengkap semua komisi
- **Easy Management**: Dashboard admin yang user-friendly
- **Copy Links**: Link referral bisa di-copy dengan mudah
- **Currency Format**: Format mata uang Indonesia yang proper

## Security Features

- **Validation**: Referrer code divalidasi sebelum diproses
- **Transaction Safety**: Menggunakan database transactions
- **Admin Authentication**: Hanya admin yang bisa akses management
- **Data Integrity**: Foreign key constraints untuk data consistency

## Future Enhancements

- **Commission Payment**: Sistem pembayaran komisi otomatis
- **Commission Reports**: Laporan komisi bulanan/tahunan
- **Commission Alerts**: Notifikasi ketika komisi mencapai threshold
- **Commission Analytics**: Analisis performa referrer
- **Commission Tiers**: Sistem tier berdasarkan jumlah referral
