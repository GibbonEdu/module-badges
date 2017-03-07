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

if (isActionAccessible($guid, $connection2, '/modules/Badges/badges_view_available.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo __($guid, 'You do not have access to this action.');
    echo '</div>';
} else {
    //Proceed!
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>".__($guid, 'Home')."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".__($guid, getModuleName($_GET['q']))."</a> > </div><div class='trailEnd'>".__($guid, 'View Available Badges').'</div>';
    echo '</div>';

    $search = null;
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
    }
    $category = null;
    if (isset($_GET['category'])) {
        $category = $_GET['category'];
    }

    echo "<h2 class='top'>";
    echo __('Search & Filter');
    echo '</h2>';
    ?>
	<form method="get" action="<?php echo $_SESSION[$guid]['absoluteURL']?>/index.php">
		<table class='smallIntBorder' cellspacing='0' style="width: 100%">
			<tr>
				<td>
					<b>Search For</b><br/>
					<span style="font-size: 90%"><i>Name</i></span>
				</td>
				<td class="right">
					<input name="search" id="search" maxlength=20 value="<?php echo $search ?>" type="text" style="width: 300px">
				</td>
			</tr>
    		<tr>
    			<td>
    				<b><?php echo __($guid, 'Category') ?></b><br/>
    				<span class="emphasis small"></span>
    			</td>
    			<td class="right">
    				<?php
    				echo "<select name='category' id='category' style='width:302px'>";
    				echo "<option value=''></option>";
    				try {
    					$dataSelect = array();
    					$sqlSelect = "SELECT DISTINCT category FROM badgesBadge WHERE active='Y' ORDER BY category";
    					$resultSelect = $connection2->prepare($sqlSelect);
    					$resultSelect->execute($dataSelect);
    				} catch (PDOException $e) {
    				}
    				while ($rowSelect = $resultSelect->fetch()) {
    					$selected = '';
    					if ($rowSelect['category'] == $category) {
    						$selected = 'selected';
    					}
    					echo "<option $selected value='".$rowSelect['category']."'>".$rowSelect['category'].'</option>';
    				}
    				echo '</select>'; ?>
    			</td>
    		</tr>
			<tr>
				<td colspan=2 class="right">
					<input type="hidden" name="q" value="/modules/<?php echo $_SESSION[$guid]['module'] ?>/badges_view_available.php">
					<input type="hidden" name="address" value="<?php echo $_SESSION[$guid]['address'] ?>">
					<?php
                    echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.$_SESSION[$guid]['module']."/badges_view_available.php'>Clear Search</a> "; ?>
					<input type="submit" value="Submit">
				</td>
			</tr>
		</table>
	</form>

	<?php
    echo "<h2 class='top'>";
    echo 'View';
    echo '</h2>';

    try {
        $data = array();
        $sqlWhere = '';
        if ($search != '' || $category != '') {
            $sqlWhere = 'WHERE ';
            if ($search != '') {
                $data['search'] = "%$search%";
                $sqlWhere .= 'badgesBadge.name LIKE :search AND ';
            }
            if ($category != '') {
                $data['category'] = $category;
                $sqlWhere .= 'badgesBadge.category=:category';
            }
            if (mb_substr($sqlWhere, -5) == ' AND ') {
                $sqlWhere = mb_substr($sqlWhere, 0, -5);
            }
        }
        $sql = "SELECT badgesBadge.* FROM badgesBadge $sqlWhere ORDER BY category, badgesBadge.name";
        $result = $connection2->prepare($sql);
        $result->execute($data);
    } catch (PDOException $e) { echo "<div class='error'>".$e->getMessage().'</div>';
    }

    if ($result->rowCount() < 1) {
        echo "<div class='warning'>";
        echo __($guid, 'There are no records to display.');
        echo '</div>';
    } else {
        $count = 0;
        $columns = 3;
        echo "<table class='margin-bottom: 10px; smallIntBorder' cellspacing='0' style='width:100%'>";
        while ($row = $result->fetch()) {
            if ($count % $columns == 0) {
                echo '<tr>';
            }

            echo "<td style='padding-top: 15px!important; padding-bottom: 15px!important; width:33%; text-align: center; vertical-align: top'>";
            if ($row['logo'] != '') {
                echo "<img style='margin-bottom: 20px; max-width: 150px' src='".$_SESSION[$guid]['absoluteURL'].'/'.$row['logo']."'/><br/>";
            } else {
                echo "<img style='margin-bottom: 20px; max-width: 150px' src='".$_SESSION[$guid]['absoluteURL'].'/themes/'.$_SESSION[$guid]['gibbonThemeName']."/img/anonymous_240_square.jpg'/><br/>";
            }
            echo '<b>'.$row['name'].'</b><br/>';
            echo '<span class=\'emphasis small\'>'.$row['category'].'</span><br/>';
            echo '</td>';

            if ($count % $columns == ($columns - 1)) {
                echo '</tr>';
            }
            ++$count;
        }

        if ($count % $columns != 0) {
            for ($i = 0;$i < $columns - ($count % $columns);++$i) {
                echo '<td></td>';
            }
            echo '</tr>';
        }
    }
    echo '</table>';
}
?>
