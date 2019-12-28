<?php

$access = '
CREATE TABLE `db_access` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`access` VARCHAR(32) NOT NULL, 
	PRIMARY KEY (`id`),
	UNIQUE (`access`)
) ENGINE = InnoDB;';
