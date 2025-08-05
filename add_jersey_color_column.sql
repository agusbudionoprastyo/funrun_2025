-- Add jersey_color column to users table
ALTER TABLE users ADD COLUMN jersey_color VARCHAR(20) DEFAULT 'darkblue' AFTER size;

-- Update existing records to have a default jersey color
UPDATE users SET jersey_color = 'darkblue' WHERE jersey_color IS NULL; 