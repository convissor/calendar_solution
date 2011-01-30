BEGIN;
DELETE FROM cs_frequent_event WHERE frequent_event LIKE 'event __';
DELETE FROM cs_category WHERE category LIKE 'category __';
DELETE FROM cs_calendar WHERE title LIKE 'title ___';
COMMIT;
