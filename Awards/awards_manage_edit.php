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

@session_start() ;

//Module includes
include "./modules/Awards/moduleFunctions.php" ;


if (isActionAccessible($guid, $connection2, "/modules/Awards/awards_manage_edit.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print "You do not have access to this action." ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>Home</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . getModuleName($_GET["q"]) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/awards_manage.php'>" . _('Manage Awards') . "</a> > </div><div class='trailEnd'>" . _('Edit Award') . "</div>" ;
	print "</div>" ;
	
	if (isset($_GET["updateReturn"])) { $updateReturn=$_GET["updateReturn"] ; } else { $updateReturn="" ; }
	$updateReturnMessage ="" ;
	$class="error" ;
	if (!($updateReturn=="")) {
		if ($updateReturn=="fail0") {
			$updateReturnMessage ="Update failed because you do not have access to this action." ;	
		}
		else if ($updateReturn=="fail1") {
			$updateReturnMessage ="Update failed because a required parameter was not set." ;	
		}
		else if ($updateReturn=="fail2") {
			$updateReturnMessage ="Update failed due to a database error." ;	
		}
		else if ($updateReturn=="fail3") {
			$updateReturnMessage ="Update failed because your inputs were invalid." ;	
		}
		else if ($updateReturn=="fail4") {
			$updateReturnMessage ="Update failed some values need to be unique but were not." ;	
		}
		else if ($updateReturn=="fail5") {
			$updateReturnMessage ="Update failed because your attachment could not be uploaded." ;	
		}
		else if ($updateReturn=="success0") {
			$updateReturnMessage ="Update was successful." ;	
			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $updateReturnMessage;
		print "</div>" ;
	} 
	
	//Check if school year specified
	$awardsAwardID=$_GET["awardsAwardID"];
	if ($awardsAwardID=="") {
		print "<div class='error'>" ;
			print "You have not specified a policy." ;
		print "</div>" ;
	}
	else {
		try {
			$data=array("awardsAwardID"=>$awardsAwardID);  
			$sql="SELECT * FROM awardsAward WHERE awardsAwardID=:awardsAwardID" ;
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			print "<div class='error'>" . $e->getMessage() . "</div>" ; 
		}
		
		if ($result->rowCount()!=1) {
			print "<div class='error'>" ;
				print "The selected policy does not exist." ;
			print "</div>" ;
		}
		else {
			//Let's go!
			$row=$result->fetch() ;
			
			if ($_GET["search"]!="") {
				print "<div class='linkTop'>" ;
					print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Awards/awards_manage.php&search=" . $_GET["search"] . "'>Back to Search Results</a>" ;
				print "</div>" ;
			}
			?>
			<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/Awards/awards_manage_editProcess.php?awardsAwardID=$awardsAwardID&search=" . $_GET["search"] ?>" enctype="multipart/form-data">
				<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
					<tr>
						<td> 
							<b>Name *</b><br/>
						</td>
						<td class="right">
							<input name="name" id="name" maxlength=100 value="<?php print htmlPrep($row["name"]) ?>" type="text" style="width: 300px">
							<script type="text/javascript">
								var name=new LiveValidation('name');
								name.add(Validate.Presence);
							</script>
						</td>
					</tr>
					<tr>
						<td> 
							<b>Active *</b><br/>
							<span style="font-size: 90%"><i></i></span>
						</td>
						<td class="right">
							<select name="active" id="active" style="width: 302px">
								<option <?php if ($row["active"]=="Y") { print "selected" ; } ?> value="Y">Y</option>
								<option <?php if ($row["active"]=="N") { print "selected" ; } ?> value="N">N</option>
							</select>
						</td>
					</tr>
					<?php
					$categories=getSettingByScope($connection2, "Awards", "awardCategories") ;
					$categories=explode(",", $categories) ;
					?>
					<tr>
						<td> 
							<b><?php print _('Category') ?> *</b><br/>
							<span style="font-size: 90%"><i></i></span>
						</td>
						<td class="right">
							<select name="category" id="category" style="width: 302px">
								<option value="Please select..."><?php print _('Please select...') ?></option>
								<?php
								for ($i=0; $i<count($categories); $i++) {
									$selected="" ;
									if ($row["category"]==$categories[$i]) {
										$selected="selected" ;
									}
									?>
									<option <?php print $selected ?> value="<?php print trim($categories[$i]) ?>"><?php print trim($categories[$i]) ?></option>
								<?php
								}
								?>
							</select>
							<script type="text/javascript">
								var category=new LiveValidation('category');
								category.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print _('Select something!') ?>"});
							</script>
						</td>
					</tr>
					<tr>
						<td> 
							<b>Description</b><br/>
						</td>
						<td class="right">
							<textarea name='description' id='description' rows=5 style='width: 300px'><?php print htmlPrep($row["description"]) ?></textarea>
						</td>
					</tr>
					<tr>
						<td> 
							<b>Logo</b><br/>
							<span style="font-size: 90%"><i><?php print _('240px x 240px') . "<br/>" ?>
							<?php if ($row["logo"]!="") {
								print _('Will overwrite existing attachment.') ;
							} ?>
							</i></span>
						</td>
						<td class="right">
							<?php
							if ($row["logo"]!="") {
								print _("Current attachment:") . " <a target='_blank' href='" . $_SESSION[$guid]["absoluteURL"] . "/" . $row["logo"] . "'>" . $row["logo"] . "</a><br/><br/>" ;
							}
							?>
							<input type="file" name="file" id="file">
							<script type="text/javascript">
								var file=new LiveValidation('file');
								file.add( Validate.Inclusion, { within: ['gif','jpg','jpeg','png'], failureMessage: "Illegal file type!", partialMatch: true, caseSensitive: false } );
							</script>
						</td>
					</tr>
					<tr>
						<td> 
							<b>Logo License/Credits</b><br/>
						</td>
						<td class="right">
							<textarea name='logoLicense' id='logoLicense' rows=5 style='width: 300px'><?php print htmlPrep($row["logoLicense"]) ?></textarea>
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print _('Year Groups') ?></b><br/>
							<span style="font-size: 90%"><i><?php print _('Relevant student year groups') ?><br/></i></span>
						</td>
						<td class="right">
							<?php 
							$yearGroups=getYearGroups($connection2) ;
							if ($yearGroups=="") {
								print "<i>" . _('No year groups available.') . "</i>" ;
							}
							else {
								for ($i=0; $i<count($yearGroups); $i=$i+2) {
									$checked="" ;
									if (is_numeric(strpos($row["gibbonYearGroupIDList"], $yearGroups[$i]))) {
										$checked="checked " ;
									}
									print _($yearGroups[($i+1)]) . " <input $checked type='checkbox' name='gibbonYearGroupIDCheck" . ($i)/2 . "'><br/>" ; 
									print "<input type='hidden' name='gibbonYearGroupID" . ($i)/2 . "' value='" . $yearGroups[$i] . "'>" ;
								}
							}
							?>
							<input type="hidden" name="count" value="<?php print (count($yearGroups))/2 ?>">
						</td>
					</tr>
			
					<tr>
						<td>
							<span style="font-size: 90%"><i>* denotes a required field</i></span>
						</td>
						<td class="right">
							<input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
							<input type="submit" value="Submit">
						</td>
					</tr>
				</table>
			</form>
			<?php
		}
	}
}
?>