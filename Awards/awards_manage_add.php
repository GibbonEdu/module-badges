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

if (isActionAccessible($guid, $connection2, "/modules/Awards/awards_manage_add.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print "You do not have access to this action." ;
	print "</div>" ;
}
else {
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>Home</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . getModuleName($_GET["q"]) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/awards_manage.php'>" . __($guid, 'Manage Awards') . "</a> > </div><div class='trailEnd'>" . __($guid, 'Add Award') . "</div>" ;
	print "</div>" ;
	
	if (isset($_GET["addReturn"])) { $addReturn=$_GET["addReturn"] ; } else { $addReturn="" ; }
	$addReturnMessage ="" ;
	$class="error" ;
	if (!($addReturn=="")) {
		if ($addReturn=="fail0") {
			$addReturnMessage ="Add failed because you do not have access to this action." ;	
		}
		else if ($addReturn=="fail2") {
			$addReturnMessage ="Add failed due to a database error." ;	
		}
		else if ($addReturn=="fail3") {
			$addReturnMessage ="Add failed because your inputs were invalid." ;	
		}
		else if ($addReturn=="fail4") {
			$addReturnMessage ="Add failed because the selected person is already registered." ;	
		}
		else if ($addReturn=="fail5") {
			$addReturnMessage ="Add succeeded, but there were problems uploading one or more attachments." ;	
		}
		else if ($addReturn=="success0") {
			$addReturnMessage ="Add was successful. You can add another record if you wish." ;	
			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $addReturnMessage;
		print "</div>" ;
	} 
	
	if ($_GET["search"]!="") {
		print "<div class='linkTop'>" ;
			print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Awards/awards_manage.php&search=" . $_GET["search"] . "'>Back to Search Results</a>" ;
		print "</div>" ;
	}
	
	?>
	<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/Awards/awards_manage_addProcess.php?search=" . $_GET["search"] ?>" enctype="multipart/form-data">
		<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
			<tr>
				<td> 
					<b>Name *</b><br/>
				</td>
				<td class="right">
					<input name="name" id="name2" maxlength=100 value="" type="text" style="width: 300px">
					<script type="text/javascript">
						var name2=new LiveValidation('name2');
						name2.add(Validate.Presence);
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
						<option value="Y">Y</option>
						<option value="N">N</option>
					</select>
				</td>
			</tr>
			<?php
			$categories=getSettingByScope($connection2, "Awards", "awardCategories") ;
			$categories=explode(",", $categories) ;
			?>
			<tr>
				<td> 
					<b><?php print __($guid, 'Category') ?> *</b><br/>
					<span style="font-size: 90%"><i></i></span>
				</td>
				<td class="right">
					<select name="category" id="category" style="width: 302px">
						<option value="Please select..."><?php print __($guid, 'Please select...') ?></option>
						<?php
						for ($i=0; $i<count($categories); $i++) {
							?>
							<option value="<?php print trim($categories[$i]) ?>"><?php print trim($categories[$i]) ?></option>
						<?php
						}
						?>
					</select>
					<script type="text/javascript">
						var category=new LiveValidation('category');
						category.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php print __($guid, 'Select something!') ?>"});
					</script>
				</td>
			</tr>
			<tr>
				<td> 
					<b>Description</b><br/>
				</td>
				<td class="right">
					<textarea name='description' id='description' rows=5 style='width: 300px'></textarea>
				</td>
			</tr>
			<tr>
				<td> 
					<b>Logo</b><br/>
					<span style="font-size: 90%"><i><?php print __($guid, '240px x 240px') ?></i></span>
				</td>
				<td class="right">
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
					<textarea name='logoLicense' id='logoLicense' rows=5 style='width: 300px'></textarea>
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print __($guid, 'Year Groups') ?></b><br/>
					<span style="font-size: 90%"><i><?php print __($guid, 'Relevant student year groups') ?><br/></i></span>
				</td>
				<td class="right">
					<?php
					print "<fieldset style='border: none'>" ;
					?>
					<script type="text/javascript">
						$(function () {
							$('.checkall').click(function () {
								$(this).parents('fieldset:eq(0)').find(':checkbox').attr('checked', this.checked);
							});
						});
					</script>
					<?php
					print __($guid, "All/None") . " <input type='checkbox' class='checkall'><br/>" ;
					$yearGroups=getYearGroups($connection2) ;
					if ($yearGroups=="") {
						print "<i>" . __($guid, 'No year groups available.') . "</i>" ;
					}
					else {
						for ($i=0; $i<count($yearGroups); $i=$i+2) {
							print __($guid, $yearGroups[($i+1)]) . " <input type='checkbox' name='gibbonYearGroupIDCheck" . ($i)/2 . "'><br/>" ; 
							print "<input type='hidden' name='gibbonYearGroupID" . ($i)/2 . "' value='" . $yearGroups[$i] . "'>" ;
						}
					}
					print "</fieldset>" ;
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
?>