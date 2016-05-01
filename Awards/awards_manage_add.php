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

@session_start();

//Module includes
include './modules/Awards/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Awards/awards_manage_add.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>Home</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".getModuleName($_GET['q'])."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q'])."/awards_manage.php'>".__($guid, 'Manage Awards')."</a> > </div><div class='trailEnd'>".__($guid, 'Add Award').'</div>';
    echo '</div>';

    $returns = array();
    $editLink = '';
    if (isset($_GET['editID'])) {
        $editLink = $_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Awards/awards_manage_edit.php&awardsAwardID='.$_GET['editID'].'&search='.$_GET['search'];
    }
    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], $editLink, null);
    }

    if ($_GET['search'] != '') {
        echo "<div class='linkTop'>";
        echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Awards/awards_manage.php&search='.$_GET['search']."'>Back to Search Results</a>";
        echo '</div>';
    }

    ?>
	<form method="post" action="<?php echo $_SESSION[$guid]['absoluteURL'].'/modules/Awards/awards_manage_addProcess.php?search='.$_GET['search'] ?>" enctype="multipart/form-data">
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
            $categories = getSettingByScope($connection2, 'Awards', 'awardCategories');
    $categories = explode(',', $categories);
    ?>
			<tr>
				<td>
					<b><?php echo __($guid, 'Category') ?> *</b><br/>
					<span style="font-size: 90%"><i></i></span>
				</td>
				<td class="right">
					<select name="category" id="category" style="width: 302px">
						<option value="Please select..."><?php echo __($guid, 'Please select...') ?></option>
						<?php
                        for ($i = 0; $i < count($categories); ++$i) {
                            ?>
							<option value="<?php echo trim($categories[$i]) ?>"><?php echo trim($categories[$i]) ?></option>
						<?php

                        }
    ?>
					</select>
					<script type="text/javascript">
						var category=new LiveValidation('category');
						category.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php echo __($guid, 'Select something!') ?>"});
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
					<span style="font-size: 90%"><i><?php echo __($guid, '240px x 240px') ?></i></span>
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
					<b><?php echo __($guid, 'Year Groups') ?></b><br/>
					<span style="font-size: 90%"><i><?php echo __($guid, 'Relevant student year groups') ?><br/></i></span>
				</td>
				<td class="right">
					<?php
                    echo "<fieldset style='border: none'>";
    ?>
					<script type="text/javascript">
						$(function () {
							$('.checkall').click(function () {
								$(this).parents('fieldset:eq(0)').find(':checkbox').attr('checked', this.checked);
							});
						});
					</script>
					<?php
                    echo __($guid, 'All/None')." <input type='checkbox' class='checkall'><br/>";
    $yearGroups = getYearGroups($connection2);
    if ($yearGroups == '') {
        echo '<i>'.__($guid, 'No year groups available.').'</i>';
    } else {
        for ($i = 0; $i < count($yearGroups); $i = $i + 2) {
            echo __($guid, $yearGroups[($i + 1)])." <input type='checkbox' name='gibbonYearGroupIDCheck".($i) / 2 ."'><br/>";
            echo "<input type='hidden' name='gibbonYearGroupID".($i) / 2 ."' value='".$yearGroups[$i]."'>";
        }
    }
    echo '</fieldset>';
    ?>
					<input type="hidden" name="count" value="<?php echo(count($yearGroups)) / 2 ?>">
				</td>
			</tr>
			<tr>
				<td>
					<span style="font-size: 90%"><i>* denotes a required field</i></span>
				</td>
				<td class="right">
					<input type="hidden" name="address" value="<?php echo $_SESSION[$guid]['address'] ?>">
					<input type="submit" value="Submit">
				</td>
			</tr>
		</table>
	</form>
	<?php

}
?>
