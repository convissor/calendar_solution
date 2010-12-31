BEGIN;
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (32768);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (65536);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (131072);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (262144);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (524288);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (1048576);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (2097152);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (4194304);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (8388608);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (16777216);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (33554432);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (67108864);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (134217728);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (268435456);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (536870912);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (1073741824);
COMMIT;

CREATE TABLE cs_category (
  category_id SERIAL,
  category VARCHAR(60) NOT NULL DEFAULT '',
  PRIMARY KEY (category_id),
  UNIQUE (category)
);

ALTER TABLE cs_calendar
  ADD category_id INTEGER DEFAULT NULL,
  ADD CONSTRAINT category_id_fk FOREIGN KEY (category_id)
    REFERENCES cs_category (category_id)
    ON UPDATE CASCADE;

