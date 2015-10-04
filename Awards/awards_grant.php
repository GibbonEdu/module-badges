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
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

@session_start() ;

//Module includes
include "./modules/" . $_SESSION[$guid]["module"] . "/moduleFunctions.php" ;

if (isActionAccessible($guid, $connection2, "/modules/Awards/awards_grant.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Get action with highest precendence
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('Grant Awards') . "</div>" ;
	print "</div>" ;
	
	if (isset($_GET["deleteReturn"])) { $deleteReturn=$_GET["deleteReturn"] ; } else { $deleteReturn="" ; }
	$deleteReturnMessage="" ;
	$class="error" ;
	if (!($deleteReturn=="")) {
		if ($deleteReturn=="success0") {
			$deleteReturnMessage=_("Your request was completed successfully.") ;		
			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $deleteReturnMessage;
		print "</div>" ;
	} 
	
	$gibbonSchoolYearID="" ;
	if (isset($_GET["gibbonSchoolYearID"])) {
		$gibbonSchoolYearID=$_GET["gibbonSchoolYearID"] ;
	}
	if ($gibbonSchoolYearID=="" OR $gibbonSchoolYearID==$_SESSION[$guid]["gibbonSchoolYearID"]) {
		$gibbonSchoolYearID=$_SESSION[$guid]["gibbonSchoolYearID"] ;
		$gibbonSchoolYearName=$_SESSION[$guid]["gibbonSchoolYearName"] ;
	}
	
	if ($gibbonSchoolYearID!=$_SESSION[$guid]["gibbonSchoolYearID"]) {
		try {
			$data=array("gibbonSchoolYearID"=>$_GET["gibbonSchoolYearID"]); 
			$sql="SELECT * FROM gibbonSchoolYear WHERE gibbonSchoolYearID=:gibbonSchoolYearID" ;
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			print "<div class='error'>" . $e->getMessage() . "</div>" ; 
		}
		if ($result->rowcount()!=1) {
			print "<div class='error'>" ;
				print _("The specified record does not exist.") ;
			print "</div>" ;
		}
		else {
			$row=$result->fetch() ;
			$gibbonSchoolYearID=$row["gibbonSchoolYearID"] ;
			$gibbonSchoolYearName=$row["name"] ;
		}
	}
	
	if ($gibbonSchoolYearID!="") {
		print "<h2>" ;
			print $gibbonSchoolYearName ;
		print "</h2>" ;
		
		print "<div class='linkTop'>" ;
			//Print year picker
			if (getPreviousSchoolYearID($gibbonSchoolYearID, $connection2)!=FALSE) {
				print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/awards_grant.php&gibbonSchoolYearID=" . getPreviousSchoolYearID($gibbonSchoolYearID, $connection2) . "'>" . _('Previous Year') . "</a> " ;
			}
			else {
				print _("Previous Year") . " " ;
			}
			print " | " ;
			if (getNextSchoolYearID($gibbonSchoolYearID, $connection2)!=FALSE) {
				print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/awards_grant.php&gibbonSchoolYearID=" . getNextSchoolYearID($gibbonSchoolYearID, $connection2) . "'>" . _('Next Year') . "</a> " ;
			}
			else {
				print _("Next Year") . " " ;
			}
		print "</div>" ;
	
		$gibbonPersonID2=NULL ;
		if (isset($_GET["gibbonPersonID2"])) {
			$gibbonPersonID2=$_GET["gibbonPersonID2"] ;
		}	
		$awardsAwardID2=NULL ;
		if (isset($_GET["awardsAwardID2"])) {
			$awardsAwardID2=$_GET["awardsAwardID2"] ;
		}	
		$gibbonYearGroupID=NULL ;
		if (isset($_GET["gibbonYearGroupID"])) {
			$gibbonYearGroupID=$_GET["gibbonYearGroupID"] ;
		}		
		$type=NULL ;
		if (isset($_GET["type"])) {
			$type=$_GET["type"] ;
		}
	
		print "<h3>" ;
			print _("Filter") ;
		print "</h3>" ;
		print "<form method='get' action='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Awards/awards_grant.php'>" ;
			print "<table class='noIntBorder' cellspacing='0' style='width: 100%'>" ;
				?>
				<tr>
					<td> 
						<b><?php print _('Student') ?></b><br/>
						<span style="font-size: 90%"><i></i></span>
					</td>
					<td class="right">
						<select name="gibbonPersonID2" id="gibbonPersonID2" style="width: 302px">
							<option value=""></option>
							<?php
							try {
								$dataSelect=array("gibbonSchoolYearID"=>$_SESSION[$guid]["gibbonSchoolYearID"]); 
								$sqlSelect="SELECT * FROM gibbonPerson JOIN gibbonStudentEnrolment ON (gibbonPerson.gibbonPersonID=gibbonStudentEnrolment.gibbonPersonID) JOIN gibbonRollGroup ON (gibbonStudentEnrolment.gibbonRollGroupID=gibbonRollGroup.gibbonRollGroupID) WHERE gibbonRollGroup.gibbonSchoolYearID=:gibbonSchoolYearID AND status='Full' AND (dateStart IS NULL OR dateStart<='" . date("Y-m-d") . "') AND (dateEnd IS NULL  OR dateEnd>='" . date("Y-m-d") . "') ORDER BY surname, preferredName" ;
								$resultSelect=$connection2->prepare($sqlSelect);
								$resultSelect->execute($dataSelect);
							}
							catch(PDOException $e) { }
							while ($rowSelect=$resultSelect->fetch()) {
								if ($gibbonPersonID2==$rowSelect["gibbonPersonID"]) {
									print "<option selected value='" . $rowSelect["gibbonPersonID"] . "'>" . formatName("", htmlPrep($rowSelect["preferredName"]), htmlPrep($rowSelect["surname"]), "Student", true) . " (" . htmlPrep($rowSelect["nameShort"]) . ")</option>" ;
								}
								else {
									print "<option value='" . $rowSelect["gibbonPersonID"] . "'>" . formatName("", htmlPrep($rowSelect["preferredName"]), htmlPrep($rowSelect["surname"]), "Student", true) . " (" . htmlPrep($rowSelect["nameShort"]) . ")</option>" ;
								}
							}
							?>			
						</select>
					</td>
				</tr>
				<tr>
					<td> 
						<b><?php print _('Award') ?></b><br/>
						<span style="font-size: 90%"><i></i></span>
					</td>
					<td class="right">
						<?php
						try {
							$dataPurpose=array(); 
							$sqlPurpose="SELECT * FROM awardsAward ORDER BY category, name" ;
							$resultPurpose=$connection2->prepare($sqlPurpose);
							$resultPurpose->execute($dataPurpose);
						}
						catch(PDOException $e) { }
					
						print "<select name='awardsAwardID2' id='awardsAwardID2' style='width: 302px'>" ;
							print "<option value=''></option>" ;
							$lastCategory="" ;
							while ($rowPurpose=$resultPurpose->fetch()) {
								$selected="" ;
								if ($rowPurpose["awardsAwardID"]==$awardsAwardID2) {
									$selected="selected" ;
								}
								$currentCategory=$rowPurpose["category"] ;
								if ($currentCategory!=$lastCategory) {
									print "<optgroup label='--" . $currentCategory . "--'>" ;
								}
								print "<option $selected value='" . $rowPurpose["awardsAwardID"] . "'>" . $rowPurpose["name"] . "</option>" ;
								$lastCategory=$currentCategory ;
							}
						print "</select>" ;
						?>
					</td>
				</tr>
				<?php
		
				print "<tr>" ;
					print "<td class='right' colspan=2>" ;
						print "<input type='hidden' name='q' value='" . $_GET["q"] . "'>" ;
						print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Awards/awards_grant.php'>" . _('Clear Filters') . "</a> " ;
						print "<input type='submit' value='" . _('Go') . "'>" ;
					print "</td>" ;
				print "</tr>" ;
			print "</table>" ;
		print "</form>" ;
	
	
		print "<h3>" ;
			print _("Awards") ;
		print "</h3>" ;
		//Set pagination variable
		$page=1 ; if (isset($_GET["page"])) { $page=$_GET["page"] ; }
		if ((!is_numeric($page)) OR $page<1) {
			$page=1 ;
		}
	
		//Search with filters applied
		try {
			$data=array() ;
			$sqlWhere="AND " ;
			if ($gibbonPersonID2!="") {
				$data["gibbonPersonID"]=$gibbonPersonID2 ;
				$sqlWhere.="awardsAwardStudent.gibbonPersonID=:gibbonPersonID AND " ; 
			}
			if ($awardsAwardID2!="") {
				$data["awardsAwardID2"]=$awardsAwardID2 ;
				$sqlWhere.="awardsAward.awardsAwardID=:awardsAwardID2 AND " ; 
			}
			if ($sqlWhere=="AND ") {
				$sqlWhere="" ;
			}
			else {
				$sqlWhere=substr($sqlWhere,0,-5) ;
			}
			$data["gibbonSchoolYearID"]=$gibbonSchoolYearID ;
			$data["gibbonSchoolYearID2"]=$gibbonSchoolYearID ;
			$sql="SELECT awardsAward.*, awardsAwardStudent.*, surname, preferredName FROM awardsAward JOIN awardsAwardStudent ON (awardsAwardStudent.awardsAwardID=awardsAward.awardsAwardID) JOIN gibbonPerson ON (awardsAwardStudent.gibbonPersonID=gibbonPerson.gibbonPersonID) JOIN gibbonStudentEnrolment ON (gibbonStudentEnrolment.gibbonPersonID=gibbonPerson.gibbonPersonID) WHERE gibbonStudentEnrolment.gibbonSchoolYearID=:gibbonSchoolYearID AND awardsAwardStudent.gibbonSchoolYearID=:gibbonSchoolYearID2 $sqlWhere ORDER BY timestamp DESC" ; 
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			print "<div class='error'>" . $e->getMessage() . "</div>" ; 
		}
		$sqlPage=$sql . " LIMIT " . $_SESSION[$guid]["pagination"] . " OFFSET " . (($page-1)*$_SESSION[$guid]["pagination"]) ;
	
		print "<div class='linkTop'>" ;
			print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/awards_grant_add.php&gibbonPersonID2=$gibbonPersonID2&awardsAwardID2=$awardsAwardID2&gibbonSchoolYearID=$gibbonSchoolYearID'>" . _('Add') . "<img style='margin: 0 0 -4px 5px' title='" . _('Add') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/page_new.png'/></a>" ;
		print "</div>" ;
	
		if ($result->rowCount()<1) {
			print "<div class='error'>" ;
			print _("There are no records to display.") ;
			print "</div>" ;
		}
		else {
			if ($result->rowCount()>$_SESSION[$guid]["pagination"]) {
				printPagination($guid, $result->rowCount(), $page, $_SESSION[$guid]["pagination"], "top", "gibbonPersonID2=$gibbonPersonID2&awardsAwardID2=$awardsAwardID2&gibbonSchoolYearID=$gibbonSchoolYearID") ;
			}
	
			print "<table cellspacing='0' style='width: 100%'>" ;
				print "<tr class='head'>" ;
					print "<th style='width: 180px'>" ;
						print _("Award") ;
					print "</th>" ;
					print "<th>" ;
						print _("Student") ;
					print "</th>" ;
					print "<th>" ;
						print _("Date") ;
					print "</th>" ;
					print "<th style='min-width: 70px'>" ;
						print _("Actions") ;
					print "</th>" ;
				print "</tr>" ;
			
				$count=0;
				$rowNum="odd" ;
				try {
					$resultPage=$connection2->prepare($sqlPage);
					$resultPage->execute($data);
				}
				catch(PDOException $e) { 
					print "<div class='error'>" . $e->getMessage() . "</div>" ; 
				}		
				while ($row=$resultPage->fetch()) {
					if ($count%2==0) {
						$rowNum="even" ;
					}
					else {
						$rowNum="odd" ;
					}
					$count++ ;
				
					//COLOR ROW BY STATUS!
					print "<tr class=$rowNum>" ;
						print "<td style='font-weight: bold; text-align: center'>" ;
							if ($row["logo"]!="") {
								print "<img class='user' style='margin-bottom: 10px; max-width: 150px' src='" . $_SESSION[$guid]["absoluteURL"] . "/" . $row["logo"] . "'/>" ;
							}
							else {
								print "<img class='user' style='margin-bottom: 10px; max-width: 150px' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/anonymous_240_square.jpg'/>" ;
							}
							print $row["name"] ;
						print "</td>" ;
						print "<td>" ;
							print "<div style='padding: 2px 0px'><b><a href='index.php?q=/modules/Students/student_view_details.php&gibbonPersonID=" . $row["gibbonPersonID"] . "&subpage=Awards&search=&allStudents=&sort=surname, preferredName'>" . formatName("", $row["preferredName"], $row["surname"], "Student", true) . "</a><br/></div>" ;
						print "</td>" ;
						print "<td>" ;
							print dateConvertBack($guid, $row["date"]) . "<br/>" ;
						print "</td>" ;
						print "<td>" ;
							print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/awards_grant_delete.php&awardsAwardStudentID=" . $row["awardsAwardStudentID"] . "&gibbonPersonID2=$gibbonPersonID2&awardsAwardID2=$awardsAwardID2&gibbonSchoolYearID=$gibbonSchoolYearID'><img title='" . _('Delete') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/garbage.png'/></a> " ;
							print "<script type='text/javascript'>" ;	
								print "$(document).ready(function(){" ;
									print "\$(\".comment-$count\").hide();" ;
									print "\$(\".show_hide-$count\").fadeIn(1000);" ;
									print "\$(\".show_hide-$count\").click(function(){" ;
									print "\$(\".comment-$count\").fadeToggle(1000);" ;
									print "});" ;
								print "});" ;
							print "</script>" ;
							if ($row["comment"]!="") {
								print "<a title='" . _('View Description') . "' class='show_hide-$count' onclick='false' href='#'><img style='padding-right: 5px' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/Default/img/page_down.png' alt='" . _('Show Comment') . "' onclick='return false;' /></a>" ;
							}
						print "</td>" ;
					print "</tr>" ;
					if ($row["comment"]!="") {
						print "<tr class='comment-$count' id='comment-$count'>" ;
							print "<td colspan=4>" ;
								if ($row["comment"]!="") {
									print "<b>" . _('Comment') . "</b><br/>" ;
									print nl2brr($row["comment"]) . "<br/><br/>" ;
								}
							print "</td>" ;
						print "</tr>" ;
					}
				}
			print "</table>" ;
		
			if ($result->rowCount()>$_SESSION[$guid]["pagination"]) {
				printPagination($guid, $result->rowCount(), $page, $_SESSION[$guid]["pagination"], "bottom", "gibbonPersonID2=$gibbonPersonID2&awardsAwardID2=$awardsAwardID2&gibbonSchoolYearID=$gibbonSchoolYearID") ;
			}
		}
	}
}	
?>