# Referral System Implementation

## Overview
This implementation adds a dynamic member referral system to the Fun Run registration platform. Users can now be referred through dynamic URLs, and administrators can track referral performance.

## Features

### 1. Dynamic Member Referral Links
- **Format**: `https://funrun.dafam.cloud/register/?member=ag`
- **Alternative**: `https://funrun.dafam.cloud/register/ag/`
- The system automatically captures the member code from the URL parameter

### 2. Database Structure
- **`users` table**: Added `referred_by` column to track who referred each user
- **`referrals` table**: Tracks referral transactions and status
- **`referrer_codes` table**: Manages valid referrer codes and names

### 3. Admin Dashboard Features
- **Referral Column**: Shows who referred each registration
- **Referral Management Page**: Complete referral statistics and management
- **Add New Referrers**: Admin can add new referrer codes

## Implementation Details

### Database Changes
```sql
-- Add referred_by column to users table
ALTER TABLE users ADD COLUMN IF NOT EXISTS referred_by VARCHAR(50) NULL;

-- Create referral tracking table
CREATE TABLE IF NOT EXISTS referrals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    referrer_code VARCHAR(50) NOT NULL,
    referred_transaction_id VARCHAR(100) NOT NULL,
    referred_name VARCHAR(255) NOT NULL,
    referral_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed') DEFAULT 'pending'
);

-- Create referrer codes table
CREATE TABLE IF NOT EXISTS referrer_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Files Modified/Created

#### New Files:
- `add_referral_system.sql` - Database setup script
- `register/process_referral.php` - Handles referral processing
- `admin/referral_management.php` - Referral management dashboard
- `admin/referral_stats.php` - API for referral statistics
- `admin/add_referrer.php` - API for adding new referrers
- `test_referral_system.php` - System testing script
- `.htaccess` - URL rewriting for dynamic member links

#### Modified Files:
- `register/register.php` - Added referral code processing
- `register/index.js` - Added URL parameter capture
- `admin/get_transactions.php` - Added referral data to queries
- `admin/index.php` - Added referral column and management link

### How It Works

1. **URL Processing**: When someone visits `?member=ag`, the JavaScript captures this parameter
2. **Form Submission**: The referral code is included in the registration form data
3. **Database Storage**: Both the user record and referral tracking are updated
4. **Admin Tracking**: Administrators can see referral information in the dashboard

### Usage Examples

#### For Referrers:
- Share link: `https://funrun.dafam.cloud/register/?member=ag`
- When someone registers through this link, they're automatically tagged as referred by "ag"

#### For Administrators:
- View referral data in the main dashboard
- Access detailed statistics at `/admin/referral_management.php`
- Add new referrer codes through the management interface

### Testing

Run the test script to verify the system:
```
https://funrun.dafam.cloud/test_referral_system.php
```

### Default Referrer Codes
The system comes with these default referrer codes:
- `ag` - Admin User
- `admin` - Administrator  
- `dafam` - Dafam Team
- `runner` - Runner Community

### Security Features
- Referrer codes are validated against the `referrer_codes` table
- Only active referrer codes are accepted
- Duplicate referrals are prevented with unique constraints
- Admin authentication required for management functions

## Installation Steps

1. **Run Database Script**:
   ```bash
   mysql -u username -p database_name < add_referral_system.sql
   ```

2. **Upload Files**: All modified and new files should be uploaded to the server

3. **Test the System**: Visit the test script to verify everything works

4. **Configure .htaccess**: Ensure mod_rewrite is enabled for URL rewriting

## Benefits

- **Track Performance**: See which referrers bring in the most registrations
- **Easy Sharing**: Simple URLs for referrers to share
- **Admin Control**: Full management of referrer codes and statistics
- **Seamless Integration**: Works with existing registration flow
- **Data Integrity**: Proper tracking and validation of all referrals
