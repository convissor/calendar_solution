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
  category_id INTEGER NOT NULL PRIMARY KEY,
  category VARCHAR(60) NOT NULL DEFAULT '',
  UNIQUE (category)
);

-- Contortions because no ALTER TABLE in v2 and limited one in v3.
BEGIN TRANSACTION;
CREATE TEMPORARY TABLE cs_calendar_backup(
  calendar_id,
  date_start,
  time_start,
  time_end,
  title,
  summary,
  location_start,
  changed,
  frequent_event_id,
  detail,
  note,
  list_link_goes_to_id,
  calendar_uri,
  status_id,
  feature_on_page_id);

INSERT INTO cs_calendar_backup SELECT 
  calendar_id,
  date_start,
  time_start,
  time_end,
  title,
  summary,
  location_start,
  changed,
  frequent_event_id,
  detail,
  note,
  list_link_goes_to_id,
  calendar_uri,
  status_id,
  feature_on_page_id
  FROM cs_calendar;

DROP TABLE cs_calendar;

CREATE TABLE cs_calendar (
  calendar_id INTEGER NOT NULL PRIMARY KEY,
  date_start DATE NOT NULL DEFAULT '1753-01-01',
  time_start TIME DEFAULT NULL,
  time_end TIME DEFAULT NULL,
  title VARCHAR(40) NOT NULL DEFAULT '',
  summary VARCHAR(250) DEFAULT NULL,
  location_start VARCHAR(250) DEFAULT NULL,
  changed CHAR(1) NOT NULL DEFAULT 'N',
  frequent_event_id INT DEFAULT NULL,
  detail TEXT,
  note VARCHAR(250) DEFAULT NULL,
  list_link_goes_to_id SMALLINT NOT NULL DEFAULT 2,
  calendar_uri VARCHAR(250) DEFAULT NULL,
  status_id SMALLINT NOT NULL DEFAULT 1,
  feature_on_page_id INT DEFAULT NULL, -- Bitwise representation of cs_feature_on_page
  category_id INTEGER DEFAULT NULL,
  CONSTRAINT category_id_fk FOREIGN KEY (category_id)
    REFERENCES cs_category (category_id)
    ON UPDATE CASCADE,
  CONSTRAINT feature_on_page_id_fk FOREIGN KEY (feature_on_page_id)
    REFERENCES cs_feature_on_page (feature_on_page_id)
    ON UPDATE CASCADE,
  CONSTRAINT frequent_event_id_fk FOREIGN KEY (frequent_event_id)
    REFERENCES cs_frequent_event (frequent_event_id)
    ON UPDATE CASCADE,
  CONSTRAINT list_link_goes_to_id_fk FOREIGN KEY (list_link_goes_to_id)
    REFERENCES cs_list_link_goes_to (list_link_goes_to_id)
    ON UPDATE CASCADE,
  CONSTRAINT status_id_fk FOREIGN KEY (status_id)
    REFERENCES cs_status (status_id)
    ON UPDATE CASCADE
);
CREATE INDEX date_start_idx ON cs_calendar (date_start);

INSERT INTO cs_calendar (
    calendar_id,
    date_start,
    time_start,
    time_end,
    title,
    summary,
    location_start,
    changed,
    frequent_event_id,
    detail,
    note,
    list_link_goes_to_id,
    calendar_uri,
    status_id,
    feature_on_page_id
  )
  SELECT
    calendar_id,
    date_start,
    time_start,
    time_end,
    title,
    summary,
    location_start,
    changed,
    frequent_event_id,
    detail,
    note,
    list_link_goes_to_id,
    calendar_uri,
    status_id,
    feature_on_page_id
  FROM cs_calendar_backup;

DROP TABLE cs_calendar_backup;
COMMIT;

