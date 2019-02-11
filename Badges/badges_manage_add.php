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

//Module includes
include './modules/Badges/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Badges/badges_manage_add.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    //Proceed!
    $page->breadcrumbs
            ->add(__('Manage Badges'),'badges_manage.php')
            ->add(__('Add Badges'));

    $returns = array();
    $editLink = '';
    if (isset($_GET['editID'])) {
        $editLink = $_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Badges/badges_manage_edit.php&badgesBadgeID='.$_GET['editID'].'&search='.$_GET['search'].'&category='.$_GET['category'];
    }
    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], $editLink, null);
    }

    if ($_GET['search'] != '' || $_GET['category'] != '') {
        echo "<div class='linkTop'>";
        echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Badges/badges_manage.php&search='.$_GET['search'].'&category='.$_GET['category']."'>Back to Search Results</a>";
        echo '</div>';
    }

    ?>
	<form method="post" action="<?php echo $_SESSION[$guid]['absoluteURL'].'/modules/Badges/badges_manage_addProcess.php?search='.$_GET['search'].'&category='.$_GET['category'] ?>" enctype="multipart/form-data">
		<table class='smallIntBorder' cellspacing='0' style="width: 100%">
			<tr>
				<td>
					<b>Name *</b><br/>
				</td>
				<td class="right">
					<input name="name" id="name2" maxlength=50 value="" type="text" style="width: 300px">
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
            $categories = getSettingByScope($connection2, 'Badges', 'badgeCategories');
			$categories = explode(',', $categories);
			?>
			<tr>
				<td>
					<b><?php echo __('Category') ?> *</b><br/>
					<span style="font-size: 90%"><i></i></span>
				</td>
				<td class="right">
					<select name="category" id="category" style="width: 302px">
						<option value="Please select..."><?php echo __('Please select...') ?></option>
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
						category.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php echo __('Select something!') ?>"});
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
					<span style="font-size: 90%"><i><?php echo __('240px x 240px') ?></i></span>
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
