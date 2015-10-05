<?php
//USE ;end TO SEPERATE SQL STATEMENTS. DON'T USE ;end IN ANY OTHER PLACES!

$sql=array() ;
$count=0 ;

//v0.1.00
$sql[$count][0]="0.1.00" ;
$sql[$count][1]="-- First version, nothing to update" ;

//v0.2.00
$count++ ;
$sql[$count][0]="0.2.00" ;
$sql[$count][1]="CREATE TABLE `awardsAwardStudent` (  `awardsAwardStudentID` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,  `awardsAwardID` int(8) unsigned zerofill NOT NULL,  `gibbonSchoolYearID` int(3) unsigned zerofill NOT NULL,  `date` date NOT NULL,  `gibbonPersonID` int(10) unsigned zerofill NOT NULL,  `comment` text CHARACTER SET utf8 NOT NULL,  `gibbonPersonIDCreator` int(10) unsigned zerofill NOT NULL, `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (`awardsAwardStudentID`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES ((SELECT gibbonModuleID FROM gibbonModule WHERE name='Awards'), 'Grant Awards', 0, 'Manage Awards', 'Allows a user to give out awards to students.', 'awards_grant.php, awards_grant_add.php, awards_grant_delete.php', 'awards_grant.php', 'Y', 'N', 'N', 'N', 'N', 'Y', 'Y', 'Y', 'Y') ;end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '1', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Awards' AND gibbonAction.name='Grant Awards'));end
" ;

//v0.5.00
$count++ ;
$sql[$count][0]="0.5.00" ;
$sql[$count][1]="INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES ((SELECT gibbonModuleID FROM gibbonModule WHERE name='Awards'), 'View Awards_my', 0, 'View Awards', 'Allows a user to view awards that they have been granted.', 'awards_view.php', 'awards_view.php', 'N', 'N', 'Y', 'N', 'N', 'N', 'Y', 'N', 'N') ;end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '3', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Awards' AND gibbonAction.name='View Awards_my'));end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES ((SELECT gibbonModuleID FROM gibbonModule WHERE name='Awards'), 'View Awards_myChildren', 1, 'View Awards', 'Allows parents to view awards that have have been granted to their children.', 'awards_view.php', 'awards_view.php', 'N', 'N', 'N', 'Y', 'N', 'N', 'N', 'Y', 'N') ;end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '4', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Awards' AND gibbonAction.name='View Awards_myChildren'));end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES ((SELECT gibbonModuleID FROM gibbonModule WHERE name='Awards'), 'View Awards_all', 2, 'View Awards', 'Allows a user to view awards that have been granted to any student.', 'awards_view.php', 'awards_view.php', 'Y', 'N', 'N', 'N', 'N', 'Y', 'Y', 'Y', 'Y') ;end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '1', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Awards' AND gibbonAction.name='View Awards_all'));end
" ;

//v1.0.00
$count++ ;
$sql[$count][0]="1.0.00" ;
$sql[$count][1]="INSERT INTO `gibbonHook` (`name`, `type`, `options`, `gibbonModuleID`) VALUES ('Awards', 'Student Profile', 'a:3:{s:16:\"sourceModuleName\";s:6:\"Awards\";s:18:\"sourceModuleAction\";s:15:\"View Awards_all\";s:19:\"sourceModuleInclude\";s:34:\"hook_studentProfile_awardsView.php\";}', 0154),('Awards', 'Parental Dashboard', 'a:3:{s:16:\"sourceModuleName\";s:6:\"Awards\";s:18:\"sourceModuleAction\";s:22:\"View Awards_myChildren\";s:19:\"sourceModuleInclude\";s:37:\"hook_parentalDashboard_awardsView.php\";}', 0154);end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '2', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Awards' AND gibbonAction.name='View Awards_all'));end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES ((SELECT gibbonModuleID FROM gibbonModule WHERE name='Awards'), 'Credits & Licensing', 1, 'Credits', 'Allows a user to view image credits for license images.', 'awards_credits.php', 'awards_credits.php', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y') ;end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '1', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Awards' AND gibbonAction.name='Credits & Licensing'));end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '2', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Awards' AND gibbonAction.name='Credits & Licensing'));end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '3', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Awards' AND gibbonAction.name='Credits & Licensing'));end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '4', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Awards' AND gibbonAction.name='Credits & Licensing'));end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '6', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Awards' AND gibbonAction.name='Credits & Licensing'));end
" ;





?>