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

if (isActionAccessible($guid, $connection2, '/modules/Awards/awards_grant_delete.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo __($guid, 'You do not have access to this action.');
    echo '</div>';
} else {
    //Proceed!
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>".__($guid, 'Home')."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".__($guid, getModuleName($_GET['q']))."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Awards/awards_grant.php&gibbonSchoolYearID='.$_GET['gibbonSchoolYearID']."'>".__($guid, 'Grant Awards')."</a> > </div><div class='trailEnd'>".__($guid, 'Delete').'</div>';
    echo '</div>';

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    //Check if school year specified
    $awardsAwardStudentID = $_GET['awardsAwardStudentID'];
    $gibbonSchoolYearID = $_GET['gibbonSchoolYearID'];
    if ($awardsAwardStudentID == '') {
        echo "<div class='error'>";
        echo __($guid, 'You have not specified one or more required parameters.');
        echo '</div>';
    } else {
        try {
            $data = array('awardsAwardStudentID' => $awardsAwardStudentID);
            $sql = 'SELECT * FROM awardsAwardStudent WHERE awardsAwardStudentID=:awardsAwardStudentID';
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            echo "<div class='error'>".$e->getMessage().'</div>';
        }

        if ($result->rowCount() != 1) {
            echo "<div class='error'>";
            echo __($guid, 'The selected record does not exist, or you do not have access to it.');
            echo '</div>';
        } else {
            //Let's go!
            $row = $result->fetch();

            if ($_GET['gibbonPersonID2'] != '' or $_GET['awardsAwardID2'] != '') {
                echo "<div class='linkTop'>";
                echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Awards/awards_grant.php&gibbonPersonID2='.$_GET['gibbonPersonID2'].'&awardsAwardID2='.$_GET['awardsAwardID2']."&gibbonSchoolYearID=$gibbonSchoolYearID"."'>".__($guid, 'Back to Search Results').'</a>';
                echo '</div>';
            }
            ?>
			<form method="post" action="<?php echo $_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module']."/awards_grant_deleteProcess.php?awardsAwardStudentID=$awardsAwardStudentID&gibbonPersonID2=".$_GET['gibbonPersonID2'].'&awardsAwardID2='.$_GET['awardsAwardID2']."&gibbonSchoolYearID=$gibbonSchoolYearID" ?>">
				<table class='smallIntBorder' cellspacing='0' style="width: 100%">
					<tr>
						<td>
							<b><?php echo __($guid, 'Are you sure you want to delete this record?');
            ?></b><br/>
							<span style="font-size: 90%; color: #cc0000"><i><?php echo __($guid, 'This operation cannot be undone, and may lead to loss of vital data in your system. PROCEED WITH CAUTION!');
            ?></i></span>
						</td>
						<td class="right">

						</td>
					</tr>
					<tr>
						<td>
							<input type="hidden" name="gibbonSchoolYearID" value="<?php echo $gibbonSchoolYearID ?>">
							<input type="hidden" name="address" value="<?php echo $_SESSION[$guid]['address'] ?>">
							<input type="submit" value="<?php echo __($guid, 'Yes');
            ?>">
						</td>
						<td class="right">

						</td>
					</tr>
				</table>
			</form>
			<?php

        }
    }
}
?>
