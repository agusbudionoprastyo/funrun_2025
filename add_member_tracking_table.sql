-- Create member_registrations table for tracking member referrals
CREATE TABLE IF NOT EXISTS member_registrations (
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



-- Create view for member registration statistics
CREATE OR REPLACE VIEW member_stats AS
SELECT 
    member_code,
    COUNT(id) as total_registrations,
    SUM(user_count) as total_users_registered,
    MIN(registered_at) as first_registration,
    MAX(registered_at) as last_registration
FROM member_registrations
GROUP BY member_code
ORDER BY total_users_registered DESC;
