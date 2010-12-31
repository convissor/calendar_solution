-- SQL commands to bring older versions of the Calendar Solution
-- database up to version 2.1.


-- Move details into main table.

ALTER TABLE cs_calendar
  ADD detail text;

BEGIN;
UPDATE cs_calendar
  JOIN cs_calendardetails USING (calendarid)
  SET cs_calendar.detail = cs_calendardetails.detail
  WHERE TRIM(cs_calendardetails.detail) <> '';
COMMIT;

DROP TABLE cs_calendardetails;


-- Rework frequent event id to be nullable and auto increment.

ALTER TABLE cs_calendar
  DROP FOREIGN KEY cs_calendar_ibfk_1;

RENAME TABLE cs_frequentevents TO cs_frequent_event;

ALTER TABLE cs_calendar
  CHANGE frequenteventid frequent_event_id INTEGER DEFAULT NULL;

BEGIN;
UPDATE cs_calendar SET frequent_event_id = NULL WHERE frequent_event_id = 0;
DELETE FROM cs_frequent_event WHERE frequenteventid = 0;
COMMIT;

ALTER TABLE cs_frequent_event
  DROP PRIMARY KEY,
  ADD frequent_event_id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT FIRST,
  DROP frequenteventid;

ALTER TABLE cs_calendar
  ADD FOREIGN KEY cs_calendar_calendar_id_fk (frequent_event_id)
    REFERENCES cs_frequent_event (frequent_event_id) ON UPDATE CASCADE;


-- Other column type and name changes.

ALTER TABLE cs_frequent_event
  CHANGE frequentevent frequent_event varchar(60) NOT NULL default '';

ALTER TABLE cs_calendar
  CHANGE calendarid calendar_id int NOT NULL auto_increment,
  CHANGE datestart date_start date NOT NULL default '1753-01-01',
  CHANGE timestart time_start time default NULL,
  CHANGE timeend time_end time default NULL,
  ADD note varchar(250) default NULL,
  MODIFY title VARCHAR(40) NOT NULL DEFAULT '',
  MODIFY summary varchar(250) default NULL,
  CHANGE location location_start varchar(250) default NULL;

BEGIN;
UPDATE cs_calendar SET summary = NULL WHERE summary = '';
UPDATE cs_calendar SET location_start = NULL WHERE location_start = '';
COMMIT;


-- Enhance URI functionality.

CREATE TABLE cs_list_link_goes_to (
  list_link_goes_to_id smallint NOT NULL,
  list_link_goes_to varchar(60) NOT NULL default '',
  PRIMARY KEY (list_link_goes_to_id),
  UNIQUE (list_link_goes_to)
) ENGINE=InnoDB;

BEGIN;
INSERT INTO cs_list_link_goes_to (list_link_goes_to_id, list_link_goes_to)
  VALUES (1, 'No Link');
INSERT INTO cs_list_link_goes_to (list_link_goes_to_id, list_link_goes_to)
  VALUES (2, 'Detail Page');
INSERT INTO cs_list_link_goes_to (list_link_goes_to_id, list_link_goes_to)
  VALUES (3, 'Frequent Event URL');
INSERT INTO cs_list_link_goes_to (list_link_goes_to_id, list_link_goes_to)
  VALUES (4, 'Calendar URL');
COMMIT;

ALTER TABLE cs_calendar
  ADD COLUMN list_link_goes_to_id smallint default 2 NOT NULL,
  ADD COLUMN calendar_uri varchar(250) default NULL,
  ADD FOREIGN KEY (list_link_goes_to_id)
    REFERENCES cs_list_link_goes_to (list_link_goes_to_id)
    ON UPDATE CASCADE;

ALTER TABLE cs_frequent_event
  CHANGE uri frequent_event_uri varchar(250) default NULL;
BEGIN;
UPDATE cs_frequent_event SET frequent_event_uri = NULL
  WHERE frequent_event_uri = '';
COMMIT;


-- Add status functionality.

CREATE TABLE cs_status (
  status_id smallint NOT NULL,
  status varchar(60) NOT NULL default '',
  PRIMARY KEY (status_id),
  UNIQUE (status)
) ENGINE=InnoDB;

BEGIN;
INSERT INTO cs_status (status_id, status)
  VALUES (1, 'Open');
INSERT INTO cs_status (status_id, status)
  VALUES (2, 'Full');
INSERT INTO cs_status (status_id, status)
  VALUES (3, 'Cancelled');
COMMIT;

ALTER TABLE cs_calendar
  ADD COLUMN status_id smallint default 1 NOT NULL,
  ADD FOREIGN KEY (status_id)
    REFERENCES cs_status (status_id)
    ON UPDATE CASCADE;


-- Add Featured on page functionality.

CREATE TABLE cs_power_of_two (
  power_of_two_id int NOT NULL,
  PRIMARY KEY (power_of_two_id)
) ENGINE=InnoDB;

BEGIN;
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (1);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (2);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (4);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (8);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (16);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (32);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (64);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (128);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (256);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (512);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (1024);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (2048);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (4096);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (8192);
INSERT INTO cs_power_of_two (power_of_two_id) VALUES (16384);
COMMIT;

CREATE TABLE cs_feature_on_page (
  feature_on_page_id int NOT NULL,
  feature_on_page varchar(60) NOT NULL default '',
  PRIMARY KEY (feature_on_page_id),
  UNIQUE (feature_on_page)
) ENGINE=InnoDB;

ALTER TABLE cs_feature_on_page
  ADD FOREIGN KEY (feature_on_page_id)
    REFERENCES cs_power_of_two (power_of_two_id);

BEGIN;
INSERT INTO cs_feature_on_page (feature_on_page_id, feature_on_page)
  VALUES (1, 'Home Page');
COMMIT;

ALTER TABLE cs_calendar
  ADD COLUMN feature_on_page_id int default NULL;
