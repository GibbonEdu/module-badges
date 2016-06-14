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

@session_start();

//Module includes
include './modules/'.$_SESSION[$guid]['module'].'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Badges/badges_grant_add.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo __($guid, 'You do not have access to this action.');
    echo '</div>';
} else {
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>".__($guid, 'Home')."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".__($guid, getModuleName($_GET['q']))."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Badges/badges_grant.php&gibbonSchoolYearID='.$_GET['gibbonSchoolYearID']."'>".__($guid, 'Grant Badges')."</a> > </div><div class='trailEnd'>".__($guid, 'Add').'</div>';
    echo '</div>';

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    $gibbonSchoolYearID = $_GET['gibbonSchoolYearID'];

    echo "<div class='linkTop'>";
    if ($_GET['gibbonPersonID2'] != '' or $_GET['badgesBadgeID2'] != '') { echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Badges/badges_grant.php&gibbonPersonID2='.$_GET['gibbonPersonID2'].'&badgesBadgeID2='.$_GET['badgesBadgeID2']."'>".__($guid, 'Back to Search Results').'</a>';
    }
    echo '</div>';
    ?>

	<form method="post" action="<?php echo $_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module'].'/badges_grant_addProcess.php?gibbonPersonID2='.$_GET['gibbonPersonID2'].'&badgesBadgeID2='.$_GET['badgesBadgeID2']."&gibbonSchoolYearID=$gibbonSchoolYearID" ?>">
		<table class='smallIntBorder' cellspacing='0' style="width: 100%">
			<tr>
				<td style='width: 275px'>
					<b><?php echo __($guid, 'Students') ?> *</b><br/>
					<span style="font-size: 90%"><i><?php echo __($guid, 'Use Control, Command and/or Shift to select multiple.') ?> </span>
				</td>
				<td class="right">
					<select multiple name="gibbonPersonIDMulti[]" id="gibbonPersonIDMulti[]" style="width: 302px; height:150px">
						<optgroup label='--<?php echo __($guid, 'Students by Roll Group') ?>--'>
							<?php
                            try {
                                $dataSelect = array('gibbonSchoolYearID' => $_SESSION[$guid]['gibbonSchoolYearID']);
                                $sqlSelect = "SELECT gibbonPerson.gibbonPersonID, preferredName, surname, gibbonRollGroup.name AS name FROM gibbonPerson, gibbonStudentEnrolment, gibbonRollGroup WHERE gibbonPerson.gibbonPersonID=gibbonStudentEnrolment.gibbonPersonID AND gibbonStudentEnrolment.gibbonRollGroupID=gibbonRollGroup.gibbonRollGroupID AND status='FULL' AND (dateStart IS NULL OR dateStart<='".date('Y-m-d')."') AND (dateEnd IS NULL  OR dateEnd>='".date('Y-m-d')."') AND gibbonRollGroup.gibbonSchoolYearID=:gibbonSchoolYearID ORDER BY name, surname, preferredName";
                                $resultSelect = $connection2->prepare($sqlSelect);
                                $resultSelect->execute($dataSelect);
                            } catch (PDOException $e) {
                            }
							while ($rowSelect = $resultSelect->fetch()) {
								echo "<option value='".$rowSelect['gibbonPersonID']."'>".htmlPrep($rowSelect['name']).' - '.formatName('', htmlPrep($rowSelect['preferredName']), htmlPrep($rowSelect['surname']), 'Student', true).'</option>';
							}
							?>
						</optgroup>
						<optgroup label='--<?php echo __($guid, 'All Users') ?>--'>
							<?php
                            try {
                                $dataSelect = array();
                                $sqlSelect = "SELECT gibbonPerson.gibbonPersonID, preferredName, surname, username FROM gibbonPerson WHERE status='Full' AND (dateStart IS NULL OR dateStart<='".date('Y-m-d')."') AND (dateEnd IS NULL  OR dateEnd>='".date('Y-m-d')."') ORDER BY surname, preferredName";
                                $resultSelect = $connection2->prepare($sqlSelect);
                                $resultSelect->execute($dataSelect);
                            } catch (PDOException $e) {
                            }
							while ($rowSelect = $resultSelect->fetch()) {
								echo "<option value='".$rowSelect['gibbonPersonID']."'>".formatName('', htmlPrep($rowSelect['preferredName']), htmlPrep($rowSelect['surname']), 'Student', true).' ('.htmlPrep($rowSelect['username']).')</option>';
							}
							?>
						</optgroup>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo __($guid, 'Badge') ?> *</b><br/>
					<span style="font-size: 90%"><i></i></span>
				</td>
				<td class="right">
					<?php
                    try {
                        $dataPurpose = array();
                        $sqlPurpose = "SELECT * FROM badgesBadge WHERE active='Y' ORDER BY category, name";
                        $resultPurpose = $connection2->prepare($sqlPurpose);
                        $resultPurpose->execute($dataPurpose);
                    } catch (PDOException $e) {
                    }

					echo "<select name='badgesBadgeID' id='badgesBadgeID' style='width: 302px'>";
					echo "<option value='Please select...'>Please select...</option>";
					$lastCategory = '';
					while ($rowPurpose = $resultPurpose->fetch()) {
						$currentCategory = $rowPurpose['category'];
						if ($currentCategory != $lastCategory) {
							echo "<optgroup label='--".$currentCategory."--'>";
						}
						echo "<option value='".$rowPurpose['badgesBadgeID']."'>".$rowPurpose['name'].'</option>';
						$lastCategory = $currentCategory;
					}
					echo '</select>';
					?>
					<script type="text/javascript">
						var badgesBadgeID=new LiveValidation('badgesBadgeID');
						badgesBadgeID.add(Validate.Exclusion, { within: ['Please select...'], failureMessage: "<?php echo __($guid, 'Select something!') ?>"});
					</script>
				</td>
			</tr>
			<tr>
				<td>
					<b><?php echo __($guid, 'Date') ?> *</b><br/>
					<span style="font-size: 90%"><i><?php echo __($guid, 'Format:') ?> <?php if ($_SESSION[$guid]['i18n']['dateFormat'] == '') {
						echo 'dd/mm/yyyy';
					} else {
						echo $_SESSION[$guid]['i18n']['dateFormat'];
					}
					?></i></span>
				</td>
				<td class="right">
					<input name="date" id="date" maxlength=10 value="<?php echo date($_SESSION[$guid]['i18n']['dateFormatPHP']) ?>" type="text" style="width: 300px">
					<script type="text/javascript">
						var date=new LiveValidation('date');
						date.add( Validate.Format, {pattern: <?php if ($_SESSION[$guid]['i18n']['dateFormatRegEx'] == '') {
							echo "/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/i";
						} else {
							echo $_SESSION[$guid]['i18n']['dateFormatRegEx'];
						}
						?>, failureMessage: "Use <?php if ($_SESSION[$guid]['i18n']['dateFormat'] == '') {
							echo 'dd/mm/yyyy';
						} else {
							echo $_SESSION[$guid]['i18n']['dateFormat'];
						}
						?>." } );
					</script>
					 <script type="text/javascript">
						$(function() {
							$( "#date" ).datepicker();
						});
					</script>
				</td>
			</tr>
			<tr>
				<td colspan=2>
					<b><?php echo __($guid, 'Comment') ?></b><br/>
					<textarea name="comment" id="comment" rows=8 style="width: 100%"></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<span style="font-size: 90%"><i>* <?php echo __($guid, 'denotes a required field'); ?></i></span>
				</td>
				<td class="right">
					<input type="hidden" name="address" value="<?php echo $_SESSION[$guid]['address'] ?>">
					<input type="hidden" name="gibbonSchoolYearID" value="<?php echo $gibbonSchoolYearID ?>">
					<input type="submit" value="<?php echo __($guid, 'Next') ?>">
				</td>
			</tr>
		</table>
	</form>
	<?php

}
?>
