CREATE TABLE cs_status (
  status_id SMALLINT NOT NULL,
  status VARCHAR(60) NOT NULL DEFAULT '',
  PRIMARY KEY (status_id),
  UNIQUE (status)
) ENGINE=InnoDB;
INSERT INTO cs_status (status_id, status)
  VALUES (1, 'Open');
INSERT INTO cs_status (status_id, status)
  VALUES (2, 'Full');
INSERT INTO cs_status (status_id, status)
  VALUES (3, 'Cancelled');

CREATE TABLE cs_list_link_goes_to (
  list_link_goes_to_id SMALLINT NOT NULL,
  list_link_goes_to VARCHAR(60) NOT NULL DEFAULT '',
  PRIMARY KEY (list_link_goes_to_id),
  UNIQUE (list_link_goes_to)
) ENGINE=InnoDB;
INSERT INTO cs_list_link_goes_to (list_link_goes_to_id, list_link_goes_to)
  VALUES (1, 'No Link');
INSERT INTO cs_list_link_goes_to (list_link_goes_to_id, list_link_goes_to)
  VALUES (2, 'Detail Page');
INSERT INTO cs_list_link_goes_to (list_link_goes_to_id, list_link_goes_to)
  VALUES (3, 'Frequent Event URL');
INSERT INTO cs_list_link_goes_to (list_link_goes_to_id, list_link_goes_to)
  VALUES (4, 'Calendar URL');

CREATE TABLE cs_power_of_two (
  power_of_two_id INT NOT NULL,
  PRIMARY KEY (power_of_two_id)
) ENGINE=InnoDB;
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

CREATE TABLE cs_feature_on_page (
  feature_on_page_id INT NOT NULL, -- Power of 2 for multiple choice via bitmask
  feature_on_page VARCHAR(60) NOT NULL DEFAULT '',
  PRIMARY KEY (feature_on_page_id),
  UNIQUE (feature_on_page),
  CONSTRAINT feature_on_page_id_fk FOREIGN KEY (feature_on_page_id)
    REFERENCES cs_power_of_two (power_of_two_id)
) ENGINE=InnoDB;
INSERT INTO cs_feature_on_page (feature_on_page_id, feature_on_page)
  VALUES (1, 'Home Page');

CREATE TABLE cs_frequent_event (
  frequent_event_id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
  frequent_event VARCHAR(60) NOT NULL DEFAULT '',
  frequent_event_uri VARCHAR(250) DEFAULT NULL,
  UNIQUE (frequent_event)
) ENGINE=InnoDB;

CREATE TABLE cs_calendar (
  calendar_id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
  date_start DATE NOT NULL DEFAULT '1753-01-01',
  time_start TIME DEFAULT NULL,
  time_end TIME DEFAULT NULL,
  title VARCHAR(40) NOT NULL DEFAULT '',
  summary VARCHAR(250) DEFAULT NULL,
  location_start VARCHAR(250) DEFAULT NULL,
  changed CHAR(1) NOT NULL DEFAULT 'N',
  frequent_event_id INTEGER DEFAULT NULL,
  detail TEXT,
  note VARCHAR(250) DEFAULT NULL,
  list_link_goes_to_id SMALLINT NOT NULL DEFAULT 2,
  calendar_uri VARCHAR(250) DEFAULT NULL,
  status_id SMALLINT NOT NULL DEFAULT 1,
  feature_on_page_id INT DEFAULT NULL, -- Bitwise representation of cs_feature_on_page
  CONSTRAINT frequent_event_id_fk FOREIGN KEY (frequent_event_id)
    REFERENCES cs_frequent_event (frequent_event_id)
    ON UPDATE CASCADE,
  CONSTRAINT list_link_goes_to_id_fk FOREIGN KEY (list_link_goes_to_id)
    REFERENCES cs_list_link_goes_to (list_link_goes_to_id)
    ON UPDATE CASCADE,
  CONSTRAINT status_id_fk FOREIGN KEY (status_id)
    REFERENCES cs_status (status_id)
    ON UPDATE CASCADE
) ENGINE=InnoDB;
CREATE INDEX date_start_idx ON cs_calendar (date_start);
