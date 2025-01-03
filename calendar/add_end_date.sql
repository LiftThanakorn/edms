ALTER TABLE events 
ADD COLUMN end_date datetime NOT NULL 
AFTER start_date;
