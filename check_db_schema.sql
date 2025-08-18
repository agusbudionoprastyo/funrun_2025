-- Check database schema for users table
DESCRIBE users;

-- Check if voucher_code column exists
SHOW COLUMNS FROM users LIKE 'voucher_code';

-- Check sample data
SELECT transaction_id, name, voucher_code, created_at FROM users ORDER BY created_at DESC LIMIT 5;

-- Check if there are any users with voucher codes
SELECT COUNT(*) as total_users, 
       COUNT(voucher_code) as users_with_voucher,
       COUNT(CASE WHEN voucher_code IS NOT NULL AND voucher_code != '' THEN 1 END) as users_with_valid_voucher
FROM users;
