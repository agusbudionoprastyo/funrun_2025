-- Add commission column to referrer_codes table
ALTER TABLE referrer_codes ADD COLUMN IF NOT EXISTS commission_rate DECIMAL(5,2) DEFAULT 0.00;
ALTER TABLE referrer_codes ADD COLUMN IF NOT EXISTS commission_amount DECIMAL(10,2) DEFAULT 0.00;
ALTER TABLE referrer_codes ADD COLUMN IF NOT EXISTS total_commission DECIMAL(10,2) DEFAULT 0.00;
ALTER TABLE referrer_codes ADD COLUMN IF NOT EXISTS referral_link VARCHAR(255) NULL;

-- Add commission tracking to referrals table
ALTER TABLE referrals ADD COLUMN IF NOT EXISTS commission_amount DECIMAL(10,2) DEFAULT 0.00;
ALTER TABLE referrals ADD COLUMN IF NOT EXISTS commission_paid BOOLEAN DEFAULT FALSE;
ALTER TABLE referrals ADD COLUMN IF NOT EXISTS commission_paid_date TIMESTAMP NULL;

UPDATE referrer_codes SET 
    commission_rate = 10.00,
    commission_amount = 10000.00,
    referral_link = CONCAT('https://funrun.dafam.cloud/register/?member=', code)
WHERE code = 'bluehouse';

-- Create commission transactions table
CREATE TABLE IF NOT EXISTS commission_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    referrer_code VARCHAR(50) NOT NULL,
    referral_id INT NOT NULL,
    transaction_id VARCHAR(100) NOT NULL,
    commission_amount DECIMAL(10,2) NOT NULL,
    commission_rate DECIMAL(5,2) NOT NULL,
    base_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    paid_at TIMESTAMP NULL,
    FOREIGN KEY (referral_id) REFERENCES referrals(id) ON DELETE CASCADE
);

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_commission_referrer ON commission_transactions(referrer_code);
CREATE INDEX IF NOT EXISTS idx_commission_status ON commission_transactions(status);
CREATE INDEX IF NOT EXISTS idx_referrer_codes_link ON referrer_codes(referral_link);