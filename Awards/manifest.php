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
$description="The Awards module allows a school to define and assign a range of awards to students. Awards can recognise, for example, academic, social or athletic achievement or progress." ;
$entryURL="awards_manage.php" ;
$type="Additional" ;
$category="Other" ;
$version="0.1.00" ;
$author="Ross Parker" ;
$url="http://rossparker.org" ;

//Module tables
$moduleTables[0]="CREATE TABLE `awardsAward` (
  `awardsAwardID` int(8) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL
  `category` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `active` enum('Y','N') NOT NULL,
  `logo` varchar(255) NOT NULL, 
  `gibbonYearGroupIDList` varchar(255) NOT NULL,
  `gibbonPersonIDCreator` int(8) unsigned zerofill NOT NULL,
  `timestampCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`awardsAwardID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;" ;

//Action rows
$actionRows[0]["name"]="Manage Awards" ;
$actionRows[0]["precedence"]="0";
$actionRows[0]["category"]="" ;
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

?>