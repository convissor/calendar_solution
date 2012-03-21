-- This is a supplemental upgrade script for Calendar Solution installations
-- that have been upgraded from version 2.1 to version 3.0 in the past.
--
-- If this script throws errors, do not worry.  That means your database
-- is already structured correctly.


ALTER TABLE cs_frequent_event ENGINE = InnoDB;

ALTER TABLE cs_frequent_event
  CONVERT TO CHARACTER SET utf8 COLLATE utf8_bin;

ALTER TABLE cs_calendar ENGINE = InnoDB;

ALTER TABLE cs_calendar
  CONVERT TO CHARACTER SET utf8 COLLATE utf8_bin;

ALTER TABLE cs_calendar
  ADD CONSTRAINT category_id_fk FOREIGN KEY (category_id)
    REFERENCES cs_category (category_id)
    ON UPDATE CASCADE,
  ADD CONSTRAINT frequent_event_id_fk FOREIGN KEY (frequent_event_id)
    REFERENCES cs_frequent_event (frequent_event_id)
    ON UPDATE CASCADE,
  ADD CONSTRAINT list_link_goes_to_id_fk FOREIGN KEY (list_link_goes_to_id)
    REFERENCES cs_list_link_goes_to (list_link_goes_to_id)
    ON UPDATE CASCADE,
  ADD CONSTRAINT status_id_fk FOREIGN KEY (status_id)
    REFERENCES cs_status (status_id)
    ON UPDATE CASCADE;
