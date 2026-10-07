ALTER TABLE posts ADD COLUMN item_date DATE NULL AFTER type;
UPDATE posts SET item_date = DATE(created_at) WHERE item_date IS NULL;
ALTER TABLE posts MODIFY item_date DATE NOT NULL;
