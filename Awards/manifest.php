<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

//This file describes the module, including database tables

//Basic variables
$name="Awards" ;
$description="The Awards module allows a school to define and assign a range of awards to students." ;
$entryURL="awards_manage.php" ;
$type="Additional" ;
$category="Assess" ;
$version="0.2.00" ;
$author="Ross Parker" ;
$url="http://rossparker.org" ;

//Module tables
$moduleTables[0]="CREATE TABLE `awardsAward` (
  `awardsAwardID` int(8) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `active` enum('Y','N') NOT NULL,
  `logo` varchar(255) NULL, 
  `logoLicense` text NOT NULL,
  `gibbonYearGroupIDList` varchar(255) NOT NULL,
  `gibbonPersonIDCreator` int(8) unsigned zerofill NOT NULL,
  `timestampCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`awardsAwardID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;" ;

$moduleTables[1]="CREATE TABLE `awardsAwardStudent` (
  `awardsAwardStudentID` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `awardsAwardID` int(8) unsigned zerofill NOT NULL,
  `gibbonSchoolYearID` int(3) unsigned zerofill NOT NULL,
  `date` date NOT NULL,
  `gibbonPersonID` int(10) unsigned zerofill NOT NULL,
  `comment` text CHARACTER SET utf8 NOT NULL,
  `gibbonPersonIDCreator` int(10) unsigned zerofill NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`awardsAwardStudentID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;" ;

//Settings
$moduleTables[2]="INSERT INTO `gibbonSetting` (`gibbonSystemSettingsID` ,`scope` ,`name` ,`nameDisplay` ,`description` ,`value`) VALUES (NULL , 'Awards', 'awardCategories', 'Award Categories', 'Comma-separated list of available choices for award category.', 'Academic,Athletic,Social,Other');";

//Action rows
$actionRows[0]["name"]="Manage Awards" ;
$actionRows[0]["precedence"]="0";
$actionRows[0]["category"]="Manage Awards" ;
$actionRows[0]["description"]="Allows a user to define and edit awards." ;
$actionRows[0]["URLList"]="awards_manage.php, awards_manage_add.php, awards_manage_edit.php, awards_manage_delete.php" ;
$actionRows[0]["entryURL"]="awards_manage.php" ;
$actionRows[0]["defaultPermissionAdmin"]="Y" ;
$actionRows[0]["defaultPermissionTeacher"]="N" ;
$actionRows[0]["defaultPermissionStudent"]="N" ;
$actionRows[0]["defaultPermissionParent"]="N" ;
$actionRows[0]["defaultPermissionSupport"]="N" ;
$actionRows[0]["categoryPermissionStaff"]="Y" ;
$actionRows[0]["categoryPermissionStudent"]="Y" ;
$actionRows[0]["categoryPermissionParent"]="Y" ;
$actionRows[0]["categoryPermissionOther"]="Y" ;

$actionRows[1]["name"]="Awards Settings" ;
$actionRows[1]["precedence"]="0";
$actionRows[1]["category"]="Manage Awards" ;
$actionRows[1]["description"]="Allows a user to adjust award settings." ;
$actionRows[1]["URLList"]="awardsSettings.php" ;
$actionRows[1]["entryURL"]="awardsSettings.php" ;
$actionRows[1]["defaultPermissionAdmin"]="Y" ;
$actionRows[1]["defaultPermissionTeacher"]="N" ;
$actionRows[1]["defaultPermissionStudent"]="N" ;
$actionRows[1]["defaultPermissionParent"]="N" ;
$actionRows[1]["defaultPermissionSupport"]="N" ;
$actionRows[1]["categoryPermissionStaff"]="Y" ;
$actionRows[1]["categoryPermissionStudent"]="Y" ;
$actionRows[1]["categoryPermissionParent"]="Y" ;
$actionRows[1]["categoryPermissionOther"]="Y" ;

$actionRows[2]["name"]="Grant Awards" ;
$actionRows[2]["precedence"]="0";
$actionRows[2]["category"]="Manage Awards" ;
$actionRows[2]["description"]="Allows a user to give out awards to students." ;
$actionRows[2]["URLList"]="awards_grant.php, awards_grant_add.php, awards_grant_delete.php" ;
$actionRows[2]["entryURL"]="awards_grant.php" ;
$actionRows[2]["defaultPermissionAdmin"]="Y" ;
$actionRows[2]["defaultPermissionTeacher"]="N" ;
$actionRows[2]["defaultPermissionStudent"]="N" ;
$actionRows[2]["defaultPermissionParent"]="N" ;
$actionRows[2]["defaultPermissionSupport"]="N" ;
$actionRows[2]["categoryPermissionStaff"]="Y" ;
$actionRows[2]["categoryPermissionStudent"]="Y" ;
$actionRows[2]["categoryPermissionParent"]="Y" ;
$actionRows[2]["categoryPermissionOther"]="Y" ;

?>