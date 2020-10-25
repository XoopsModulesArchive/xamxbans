#
# Table structure for table 'amx_bans'
#
CREATE TABLE amx_bans (
    bid         INT(11)      NOT NULL AUTO_INCREMENT,
    player_ip   VARCHAR(100)          DEFAULT NULL,
    player_id   VARCHAR(50)  NOT NULL DEFAULT '0',
    player_nick VARCHAR(100) NOT NULL DEFAULT 'Unknown',
    admin_ip    VARCHAR(100)          DEFAULT NULL,
    admin_id    VARCHAR(50)  NOT NULL DEFAULT '0',
    admin_nick  VARCHAR(100) NOT NULL DEFAULT 'Unknown',
    ban_type    VARCHAR(10)  NOT NULL DEFAULT 'S',
    ban_reason  VARCHAR(255) NOT NULL DEFAULT '',
    ban_created INT(11)      NOT NULL DEFAULT '0',
    ban_length  VARCHAR(100) NOT NULL DEFAULT '',
    server_ip   VARCHAR(100) NOT NULL DEFAULT '',
    server_name VARCHAR(100) NOT NULL DEFAULT 'Unknown',
    PRIMARY KEY (bid)
);

#
# Table structure for table 'amx_banhistory'
#
CREATE TABLE amx_banhistory (
    bhid             INT(11)      NOT NULL AUTO_INCREMENT,
    player_ip        VARCHAR(100)          DEFAULT NULL,
    player_id        VARCHAR(50)  NOT NULL DEFAULT '0',
    player_nick      VARCHAR(100) NOT NULL DEFAULT 'Unknown',
    admin_ip         VARCHAR(100)          DEFAULT NULL,
    admin_id         VARCHAR(50)  NOT NULL DEFAULT '0',
    admin_nick       VARCHAR(100) NOT NULL DEFAULT 'Unknown',
    ban_type         VARCHAR(10)  NOT NULL DEFAULT 'S',
    ban_reason       VARCHAR(255) NOT NULL DEFAULT '',
    ban_created      INT(11)      NOT NULL DEFAULT '0',
    ban_length       VARCHAR(100) NOT NULL DEFAULT '',
    server_ip        VARCHAR(100) NOT NULL DEFAULT '',
    server_name      VARCHAR(100) NOT NULL DEFAULT 'Unknown',
    unban_created    INT(11)      NOT NULL DEFAULT '0',
    unban_reason     VARCHAR(255) NOT NULL DEFAULT 'tempban expired',
    unban_admin_nick VARCHAR(100) NOT NULL DEFAULT 'Unknown',
    PRIMARY KEY (bhid)
);

#
# Table structure for table 'amx_amxadmins'
#
CREATE TABLE amx_amxadmins (
    id       INT(12)     NOT NULL AUTO_INCREMENT,
    username VARCHAR(32)          DEFAULT NULL,
    password VARCHAR(32)          DEFAULT NULL,
    access   VARCHAR(32)          DEFAULT NULL,
    flags    VARCHAR(32)          DEFAULT NULL,
    steamid  VARCHAR(32)          DEFAULT NULL,
    nickname VARCHAR(32) NOT NULL DEFAULT '',
    PRIMARY KEY (id)
);

#
# Table structure for table 'amx_admins_servers'
#
CREATE TABLE amx_admins_servers (
    admin_id  INT(12) NOT NULL DEFAULT '0',
    server_id INT(12) NOT NULL DEFAULT '0'
);

#
# Table structure for table 'amx_levels'
#
#CREATE TABLE amx_levels (
#  level int(12) NOT NULL default '0',
#  bans_add enum('yes','no') NOT NULL default 'no',
#  bans_edit enum('yes','no') NOT NULL default 'no',
#  bans_delete enum('yes','no') NOT NULL default 'no',
#  bans_unban enum('yes','no') NOT NULL default 'no',
#  bans_import enum('yes','no') NOT NULL default 'no',
#  bans_export enum('yes','no') NOT NULL default 'no',
#  amxadmins_view enum('yes','no') NOT NULL default 'no',
#  amxadmins_edit enum('yes','no') NOT NULL default 'no',
#  webadmins_view enum('yes','no') NOT NULL default 'no',
#  webadmins_edit enum('yes','no') NOT NULL default 'no',
#  permissions_edit enum('yes','no') NOT NULL default 'no',
#  prune_db enum('yes','no') NOT NULL default 'no',
#  servers_view enum('yes','no') NOT NULL default 'no', 
#  servers_delete enum('yes','no') NOT NULL default 'no',
#  PRIMARY KEY (level)
#);


#
# Table structure for table 'amx_levels'
# updated for xoops
CREATE TABLE amx_levels (
    level       INT(12)           NOT NULL DEFAULT '0',
    bans_add    ENUM ('yes','no') NOT NULL DEFAULT 'no',
    bans_edit   ENUM ('yes','no') NOT NULL DEFAULT 'no',
    bans_delete ENUM ('yes','no') NOT NULL DEFAULT 'no',
    bans_unban  ENUM ('yes','no') NOT NULL DEFAULT 'no',
    PRIMARY KEY (level)
);

#
# Table structure for table 'amx_serverinfo'
#
CREATE TABLE amx_serverinfo (
    id             INT(11)      NOT NULL AUTO_INCREMENT,
    timestamp      VARCHAR(50)  NOT NULL DEFAULT '0',
    hostname       VARCHAR(100) NOT NULL DEFAULT 'Unknown',
    address        VARCHAR(32)  NOT NULL DEFAULT '',
    gametype       VARCHAR(32)  NOT NULL DEFAULT '',
    rcon           VARCHAR(32)           DEFAULT NULL,
    amxban_version VARCHAR(32)  NOT NULL DEFAULT '',
    amxban_motd    VARCHAR(250) NOT NULL DEFAULT 'www.yoursite.com/modules/xamxbans/motd_details.php?bid=%s',
    motd_delay     INT(10)      NOT NULL DEFAULT '10',
    PRIMARY KEY (id)
);

#
# Table structure for table 'amx_webadmins'
#
#CREATE TABLE amx_webadmins (
#  id int(12) NOT NULL auto_increment,
#  username varchar(32) default NULL,
#  password varchar(32) default NULL,
#  level varchar(32) NOT NULL default '6',
#  logcode varchar(32) NOT NULL default '',
#  PRIMARY KEY (id)
#);

#
# Table structure for table 'amx_webadmins'
# modified for xoops
CREATE TABLE amx_webadmins (
    id            INT(12)               NOT NULL AUTO_INCREMENT,
    type          ENUM ('user','group') NOT NULL DEFAULT 'user',
    user_group_id VARCHAR(32)           NULL,
    level         VARCHAR(32)           NOT NULL DEFAULT '10',
    PRIMARY KEY (id)
);


CREATE TABLE amx_config (
    id                    INT(2)            NOT NULL AUTO_INCREMENT,
    use_logfile           ENUM ('yes','no') NOT NULL DEFAULT 'no',
    use_rcon              ENUM ('yes','no') NOT NULL DEFAULT 'no',
    show_graphs           ENUM ('yes','no') NOT NULL DEFAULT 'no',
    show_reason_in_list   ENUM ('yes','no') NOT NULL DEFAULT 'no',
    use_xhlstats          ENUM ('yes','no') NOT NULL DEFAULT 'no',
    block_max_name_length INT(11)           NOT NULL DEFAULT '20',
    block_list_rows       INT(11)           NOT NULL DEFAULT '5',
    logfile               VARCHAR(250)      NOT NULL DEFAULT '/tmp/amxbans.log',
    PRIMARY KEY (id)
);

INSERT INTO amx_levels
VALUES ('1', 'yes', 'yes', 'yes', 'yes');
INSERT INTO amx_webadmins
VALUES ('1', 'group', 1, 1);
#INSERT INTO amx_levels VALUES (1, 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes');
#INSERT INTO amx_webadmins VALUES (1, 'Superadmin', '21232f297a57a5a743894a0e4a801fc3', '1', '');
INSERT INTO amx_config
VALUES ('', 'no', 'no', 'no', 'no', 'no', 20, 5, '/tmp');




