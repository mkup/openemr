CREATE TABLE IF NOT EXISTS `form_therapy_notes` (
    /* both extended and encounter forms need a last modified date */
    date datetime default NULL comment 'last modified date',
    /* these fields are common to all encounter forms. */
    id bigint(20) NOT NULL auto_increment,
    pid bigint(20) NOT NULL default 0,
    user varchar(255) default NULL,
    groupname varchar(255) default NULL,
    authorized tinyint(4) default NULL,
    activity tinyint(4) default NULL,
    diagnosis varchar(255),
    diag_text TEXT,
    subjective TEXT,
    objective varchar(255),
    obj_text TEXT,
    assessment TEXT,
    plan TEXT,
    provider int(11) default NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

