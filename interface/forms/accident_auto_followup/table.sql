CREATE TABLE IF NOT EXISTS `form_acc_auto_followup` (
    /* both extended and encounter forms need a last modified date */
    date datetime default NULL comment 'last modified date',
    /* these fields are common to all encounter forms. */
    id bigint(20) NOT NULL auto_increment,
    pid bigint(20) NOT NULL default 0,
    user varchar(255) default NULL,
    groupname varchar(255) default NULL,
    authorized tinyint(4) default NULL,
    activity tinyint(4) default NULL,
    same_condition varchar(5),
    same_text varchar(30),
    solely_accident varchar(5),
    solely_text varchar(40),
    due_employment varchar(5),
    disability varchar(5),
    disability_text varchar(70),
    disable_from datetime default NULL,
    disable_to datetime default NULL,
    return_work datetime default NULL,
    need_rehab varchar(5),
    rehab_text varchar(80),
    PRIMARY KEY (id)
) ENGINE=InnoDB;

