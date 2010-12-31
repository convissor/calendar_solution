DELETE FROM cs_power_of_two WHERE power_of_two_id >= 32768;

ALTER TABLE cs_calendar DROP CONSTRAINT category_id_fk;
ALTER TABLE cs_calendar DROP category_id;
DROP TABLE cs_category;

