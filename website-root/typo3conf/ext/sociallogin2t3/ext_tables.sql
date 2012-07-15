#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (
	tx_sociallogin2t3_service_provider varchar(255) DEFAULT '' NOT NULL,
	tx_sociallogin2t3_id varchar(255) DEFAULT '' NOT NULL,
	tx_sociallogin2t3_first_name varchar(255) DEFAULT '' NOT NULL,
	tx_sociallogin2t3_last_name varchar(255) DEFAULT '' NOT NULL,
	tx_sociallogin2t3_gender varchar(255) DEFAULT '' NOT NULL,
	tx_sociallogin2t3_url tinytext
);