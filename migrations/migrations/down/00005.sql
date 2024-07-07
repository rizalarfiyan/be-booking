-- Migrate to Version 5
DROP TABLE IF EXISTS rating_histories;
DROP TABLE IF EXISTS histories;
ALTER TABLE books MODIFY COLUMN rating INT NOT NULL DEFAULT 0;
