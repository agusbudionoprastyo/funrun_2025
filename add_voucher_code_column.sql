-- Add voucher_code column to users table if it doesn't exist
ALTER TABLE users ADD COLUMN IF NOT EXISTS voucher_code VARCHAR(50) NULL;

-- Add index for better performance when searching by voucher code
CREATE INDEX IF NOT EXISTS idx_users_voucher_code ON users(voucher_code);

-- Optional: Add a table for voucher management
CREATE TABLE IF NOT EXISTS vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    discount_amount DECIMAL(10,2) NOT NULL,
    max_usage INT DEFAULT 1,
    current_usage INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL
);

-- Insert sample voucher codes
INSERT IGNORE INTO vouchers (code, discount_amount, max_usage, expires_at) VALUES
('SEMARANGRUNNER', 15000.00, 100, '2025-12-31 23:59:59'),
('FAKERUNNER', 15000.00, 100, '2025-12-31 23:59:59'),
('BERLARIBERSAMA', 15000.00, 100, '2025-12-31 23:59:59'),
('PLAYONAMBYAR', 15000.00, 100, '2025-12-31 23:59:59'),
('PLAYONNDESO', 15000.00, 100, '2025-12-31 23:59:59'),
('BESTIFITY', 15000.00, 100, '2025-12-31 23:59:59'),
('DURAKINGRUN', 15000.00, 100, '2025-12-31 23:59:59'),
('SALATIGARB', 15000.00, 100, '2025-12-31 23:59:59'),
('PELARIAN', 15000.00, 100, '2025-12-31 23:59:59');