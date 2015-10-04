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

?>