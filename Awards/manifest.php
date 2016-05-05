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
$name = 'Awards';
$description = 'The Awards module allows a school to define and assign a range of awards to students.';
$entryURL = 'awards_manage.php';
$type = 'Additional';
$category = 'Assess';
$version = '1.0.03';
$author = 'Ross Parker';
$url = 'http://rossparker.org';

//Module tables
$moduleTables[0] = "CREATE TABLE `awardsAward` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";

$moduleTables[1] = 'CREATE TABLE `awardsAwardStudent` (
  `awardsAwardStudentID` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `awardsAwardID` int(8) unsigned zerofill NOT NULL,
  `gibbonSchoolYearID` int(3) unsigned zerofill NOT NULL,
  `date` date NOT NULL,
  `gibbonPersonID` int(10) unsigned zerofill NOT NULL,
  `comment` text CHARACTER SET utf8 NOT NULL,
  `gibbonPersonIDCreator` int(10) unsigned zerofill NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`awardsAwardStudentID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';

//Settings
$moduleTables[2] = "INSERT INTO `gibbonSetting` (`gibbonSystemSettingsID` ,`scope` ,`name` ,`nameDisplay` ,`description` ,`value`) VALUES (NULL , 'Awards', 'awardCategories', 'Award Categories', 'Comma-separated list of available choices for award category.', 'Academic,Athletic,Social,Other');";

//Action rows
$actionRows[0]['name'] = 'Manage Awards';
$actionRows[0]['precedence'] = '0';
$actionRows[0]['category'] = 'Manage Awards';
$actionRows[0]['description'] = 'Allows a user to define and edit awards.';
$actionRows[0]['URLList'] = 'awards_manage.php, awards_manage_add.php, awards_manage_edit.php, awards_manage_delete.php';
$actionRows[0]['entryURL'] = 'awards_manage.php';
$actionRows[0]['defaultPermissionAdmin'] = 'Y';
$actionRows[0]['defaultPermissionTeacher'] = 'N';
$actionRows[0]['defaultPermissionStudent'] = 'N';
$actionRows[0]['defaultPermissionParent'] = 'N';
$actionRows[0]['defaultPermissionSupport'] = 'N';
$actionRows[0]['categoryPermissionStaff'] = 'Y';
$actionRows[0]['categoryPermissionStudent'] = 'Y';
$actionRows[0]['categoryPermissionParent'] = 'Y';
$actionRows[0]['categoryPermissionOther'] = 'Y';

$actionRows[1]['name'] = 'Awards Settings';
$actionRows[1]['precedence'] = '0';
$actionRows[1]['category'] = 'Manage Awards';
$actionRows[1]['description'] = 'Allows a user to adjust award settings.';
$actionRows[1]['URLList'] = 'awardsSettings.php';
$actionRows[1]['entryURL'] = 'awardsSettings.php';
$actionRows[1]['defaultPermissionAdmin'] = 'Y';
$actionRows[1]['defaultPermissionTeacher'] = 'N';
$actionRows[1]['defaultPermissionStudent'] = 'N';
$actionRows[1]['defaultPermissionParent'] = 'N';
$actionRows[1]['defaultPermissionSupport'] = 'N';
$actionRows[1]['categoryPermissionStaff'] = 'Y';
$actionRows[1]['categoryPermissionStudent'] = 'Y';
$actionRows[1]['categoryPermissionParent'] = 'Y';
$actionRows[1]['categoryPermissionOther'] = 'Y';

$actionRows[2]['name'] = 'Grant Awards';
$actionRows[2]['precedence'] = '0';
$actionRows[2]['category'] = 'Manage Awards';
$actionRows[2]['description'] = 'Allows a user to give out awards to students.';
$actionRows[2]['URLList'] = 'awards_grant.php, awards_grant_add.php, awards_grant_delete.php';
$actionRows[2]['entryURL'] = 'awards_grant.php';
$actionRows[2]['defaultPermissionAdmin'] = 'Y';
$actionRows[2]['defaultPermissionTeacher'] = 'N';
$actionRows[2]['defaultPermissionStudent'] = 'N';
$actionRows[2]['defaultPermissionParent'] = 'N';
$actionRows[2]['defaultPermissionSupport'] = 'N';
$actionRows[2]['categoryPermissionStaff'] = 'Y';
$actionRows[2]['categoryPermissionStudent'] = 'Y';
$actionRows[2]['categoryPermissionParent'] = 'Y';
$actionRows[2]['categoryPermissionOther'] = 'Y';

$actionRows[3]['name'] = 'View Awards_my';
$actionRows[3]['precedence'] = '0';
$actionRows[3]['category'] = 'View Awards';
$actionRows[3]['description'] = 'Allows a user to view awards that they have been granted.';
$actionRows[3]['URLList'] = 'awards_view.php';
$actionRows[3]['entryURL'] = 'awards_view.php';
$actionRows[3]['defaultPermissionAdmin'] = 'N';
$actionRows[3]['defaultPermissionTeacher'] = 'N';
$actionRows[3]['defaultPermissionStudent'] = 'Y';
$actionRows[3]['defaultPermissionParent'] = 'N';
$actionRows[3]['defaultPermissionSupport'] = 'N';
$actionRows[3]['categoryPermissionStaff'] = 'N';
$actionRows[3]['categoryPermissionStudent'] = 'Y';
$actionRows[3]['categoryPermissionParent'] = 'N';
$actionRows[3]['categoryPermissionOther'] = 'N';

$actionRows[4]['name'] = 'View Awards_myChildren';
$actionRows[4]['precedence'] = '1';
$actionRows[4]['category'] = 'View Awards';
$actionRows[4]['description'] = 'Allows parents to view awards that have have been granted to their children.';
$actionRows[4]['URLList'] = 'awards_view.php';
$actionRows[4]['entryURL'] = 'awards_view.php';
$actionRows[4]['defaultPermissionAdmin'] = 'N';
$actionRows[4]['defaultPermissionTeacher'] = 'N';
$actionRows[4]['defaultPermissionStudent'] = 'N';
$actionRows[4]['defaultPermissionParent'] = 'Y';
$actionRows[4]['defaultPermissionSupport'] = 'N';
$actionRows[4]['categoryPermissionStaff'] = 'N';
$actionRows[4]['categoryPermissionStudent'] = 'N';
$actionRows[4]['categoryPermissionParent'] = 'Y';
$actionRows[4]['categoryPermissionOther'] = 'N';

$actionRows[5]['name'] = 'View Awards_all';
$actionRows[5]['precedence'] = '2';
$actionRows[5]['category'] = 'View Awards';
$actionRows[5]['description'] = 'Allows a user to view awards that have been granted to any student.';
$actionRows[5]['URLList'] = 'awards_view.php';
$actionRows[5]['entryURL'] = 'awards_view.php';
$actionRows[5]['defaultPermissionAdmin'] = 'Y';
$actionRows[5]['defaultPermissionTeacher'] = 'Y';
$actionRows[5]['defaultPermissionStudent'] = 'N';
$actionRows[5]['defaultPermissionParent'] = 'N';
$actionRows[5]['defaultPermissionSupport'] = 'N';
$actionRows[5]['categoryPermissionStaff'] = 'Y';
$actionRows[5]['categoryPermissionStudent'] = 'Y';
$actionRows[5]['categoryPermissionParent'] = 'Y';
$actionRows[5]['categoryPermissionOther'] = 'Y';

$actionRows[6]['name'] = 'Credits & Licenses';
$actionRows[6]['precedence'] = '1';
$actionRows[6]['category'] = 'Credits';
$actionRows[6]['description'] = 'Allows a user to view image credits for license images.';
$actionRows[6]['URLList'] = 'awards_credits.php';
$actionRows[6]['entryURL'] = 'awards_credits.php';
$actionRows[6]['defaultPermissionAdmin'] = 'Y';
$actionRows[6]['defaultPermissionTeacher'] = 'Y';
$actionRows[6]['defaultPermissionStudent'] = 'Y';
$actionRows[6]['defaultPermissionParent'] = 'Y';
$actionRows[6]['defaultPermissionSupport'] = 'Y';
$actionRows[6]['categoryPermissionStaff'] = 'Y';
$actionRows[6]['categoryPermissionStudent'] = 'Y';
$actionRows[6]['categoryPermissionParent'] = 'Y';
$actionRows[6]['categoryPermissionOther'] = 'Y';

//HOOKS
$array = array();
$array['sourceModuleName'] = 'Awards';
$array['sourceModuleAction'] = 'View Awards_all';
$array['sourceModuleInclude'] = 'hook_studentProfile_awardsView.php';
$hooks[0] = "INSERT INTO `gibbonHook` (`gibbonHookID`, `name`, `type`, `options`, gibbonModuleID) VALUES (NULL, 'Awards', 'Student Profile', '".serialize($array)."', (SELECT gibbonModuleID FROM gibbonModule WHERE name='$name'));";

$array = array();
$array['sourceModuleName'] = 'Awards';
$array['sourceModuleAction'] = 'View Awards_myChildren';
$array['sourceModuleInclude'] = 'hook_parentalDashboard_awardsView.php';
$hooks[1] = "INSERT INTO `gibbonHook` (`gibbonHookID`, `name`, `type`, `options`, gibbonModuleID) VALUES (NULL, 'Awards', 'Parental Dashboard', '".serialize($array)."', (SELECT gibbonModuleID FROM gibbonModule WHERE name='$name'));";
