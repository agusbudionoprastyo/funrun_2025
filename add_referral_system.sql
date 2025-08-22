-- Add referred_by column to users table
ALTER TABLE users ADD COLUMN IF NOT EXISTS referred_by VARCHAR(50) NULL;

-- Add index for better performance when searching by referral
CREATE INDEX IF NOT EXISTS idx_users_referred_by ON users(referred_by);

-- Create referral tracking table
CREATE TABLE IF NOT EXISTS referrals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    referrer_code VARCHAR(50) NOT NULL,
    referred_transaction_id VARCHAR(100) NOT NULL,
    referred_name VARCHAR(255) NOT NULL,
    referral_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed') DEFAULT 'pending',
    UNIQUE KEY unique_referral (referrer_code, referred_transaction_id)
);

-- Add index for referral tracking
CREATE INDEX IF NOT EXISTS idx_referrals_referrer ON referrals(referrer_code);
CREATE INDEX IF NOT EXISTS idx_referrals_transaction ON referrals(referred_transaction_id);

-- Create referrer codes table for valid referrers
CREATE TABLE IF NOT EXISTS referrer_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample referrer codes
INSERT IGNORE INTO referrer_codes (code, name) VALUES
('ag', 'Admin User'),
('admin', 'Administrator'),
('dafam', 'Dafam Team'),
('runner', 'Runner Community');
