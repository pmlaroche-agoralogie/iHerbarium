#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (
	tx_facebook2t3_id varchar(255) DEFAULT '' NOT NULL,
	tx_facebook2t3_first_name varchar(255) DEFAULT '' NOT NULL,
	tx_facebook2t3_last_name varchar(255) DEFAULT '' NOT NULL,
	tx_facebook2t3_link tinytext,
	tx_facebook2t3_gender varchar(255) DEFAULT '' NOT NULL
);