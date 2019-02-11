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

//Module includes
include './modules/'.$_SESSION[$guid]['module'].'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Badges/badges_grant.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo __('You do not have access to this action.');
    echo '</div>';
} else {
    //Get action with highest precendence
    $page->breadcrumbs->add(__('Grant Badges'));

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    $gibbonSchoolYearID = '';
    if (isset($_GET['gibbonSchoolYearID'])) {
        $gibbonSchoolYearID = $_GET['gibbonSchoolYearID'];
    }
    if ($gibbonSchoolYearID == '' or $gibbonSchoolYearID == $_SESSION[$guid]['gibbonSchoolYearID']) {
        $gibbonSchoolYearID = $_SESSION[$guid]['gibbonSchoolYearID'];
        $gibbonSchoolYearName = $_SESSION[$guid]['gibbonSchoolYearName'];
    }

    if ($gibbonSchoolYearID != $_SESSION[$guid]['gibbonSchoolYearID']) {
        try {
            $data = array('gibbonSchoolYearID' => $_GET['gibbonSchoolYearID']);
            $sql = 'SELECT * FROM gibbonSchoolYear WHERE gibbonSchoolYearID=:gibbonSchoolYearID';
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            echo "<div class='error'>".$e->getMessage().'</div>';
        }
        if ($result->rowcount() != 1) {
            echo "<div class='error'>";
            echo __('The specified record does not exist.');
            echo '</div>';
        } else {
            $row = $result->fetch();
            $gibbonSchoolYearID = $row['gibbonSchoolYearID'];
            $gibbonSchoolYearName = $row['name'];
        }
    }

    if ($gibbonSchoolYearID != '') {
        echo '<h2>';
        echo $gibbonSchoolYearName;
        echo '</h2>';

        echo "<div class='linkTop'>";
        //Print year picker
        if (getPreviousSchoolYearID($gibbonSchoolYearID, $connection2) != false) {
            echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.$_SESSION[$guid]['module'].'/badges_grant.php&gibbonSchoolYearID='.getPreviousSchoolYearID($gibbonSchoolYearID, $connection2)."'>".__('Previous Year').'</a> ';
        } else {
            echo __('Previous Year').' ';
        }
        echo ' | ';
        if (getNextSchoolYearID($gibbonSchoolYearID, $connection2) != false) {
            echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.$_SESSION[$guid]['module'].'/badges_grant.php&gibbonSchoolYearID='.getNextSchoolYearID($gibbonSchoolYearID, $connection2)."'>".__('Next Year').'</a> ';
        } else {
            echo __('Next Year').' ';
        }
        echo '</div>';

        $gibbonPersonID2 = null;
        if (isset($_GET['gibbonPersonID2'])) {
            $gibbonPersonID2 = $_GET['gibbonPersonID2'];
        }
        $badgesBadgeID2 = null;
        if (isset($_GET['badgesBadgeID2'])) {
            $badgesBadgeID2 = $_GET['badgesBadgeID2'];
        }
        $gibbonYearGroupID = null;
        if (isset($_GET['gibbonYearGroupID'])) {
            $gibbonYearGroupID = $_GET['gibbonYearGroupID'];
        }
        $type = null;
        if (isset($_GET['type'])) {
            $type = $_GET['type'];
        }

        echo '<h3>';
        echo __('Filter');
        echo '</h3>';
        echo "<form method='get' action='".$_SESSION[$guid]['absoluteURL']."/index.php?q=/modules/Badges/badges_grant.php'>";
        echo "<table class='noIntBorder' cellspacing='0' style='width: 100%'>";
        ?>
        <tr>
            <td>
                <b><?php echo __('User') ?></b><br/>
                <span style="font-size: 90%"><i></i></span>
            </td>
            <td class="right">
                <select name="gibbonPersonID2" id="gibbonPersonID2" style="width: 302px">
                    <option value=""></option>
                    <?php
                    try {
                        $dataSelect = array();
                        $sqlSelect = "SELECT gibbonPerson.gibbonPersonID, preferredName, surname, username FROM gibbonPerson WHERE status='Full' AND (dateStart IS NULL OR dateStart<='".date('Y-m-d')."') AND (dateEnd IS NULL  OR dateEnd>='".date('Y-m-d')."') ORDER BY surname, preferredName";
                        $resultSelect = $connection2->prepare($sqlSelect);
                        $resultSelect->execute($dataSelect);
                    } catch (PDOException $e) {

                    }
                    while ($rowSelect = $resultSelect->fetch()) {
                        if ($gibbonPersonID2 == $rowSelect['gibbonPersonID']) {
                            echo "<option selected value='".$rowSelect['gibbonPersonID']."'>".formatName('', htmlPrep($rowSelect['preferredName']), htmlPrep($rowSelect['surname']), 'Student', true).' ('.htmlPrep($rowSelect['username']).')</option>';
                        } else {
                            echo "<option value='".$rowSelect['gibbonPersonID']."'>".formatName('', htmlPrep($rowSelect['preferredName']), htmlPrep($rowSelect['surname']), 'Student', true).' ('.htmlPrep($rowSelect['username']).')</option>';
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <b><?php echo __('Badges') ?></b><br/>
                <span style="font-size: 90%"><i></i></span>
            </td>
            <td class="right">
                <?php
                try {
                    $dataPurpose = array();
                    $sqlPurpose = 'SELECT * FROM badgesBadge ORDER BY category, name';
                    $resultPurpose = $connection2->prepare($sqlPurpose);
                    $resultPurpose->execute($dataPurpose);
                } catch (PDOException $e) {

                }

                echo "<select name='badgesBadgeID2' id='badgesBadgeID2' style='width: 302px'>";
                echo "<option value=''></option>";
                $lastCategory = '';
                while ($rowPurpose = $resultPurpose->fetch()) {
                    $selected = '';
                    if ($rowPurpose['badgesBadgeID'] == $badgesBadgeID2) {
                        $selected = 'selected';
                    }
                    $currentCategory = $rowPurpose['category'];
                    if ($currentCategory != $lastCategory) {
                        echo "<optgroup label='--".$currentCategory."--'>";
                    }
                    echo "<option $selected value='".$rowPurpose['badgesBadgeID']."'>".$rowPurpose['name'].'</option>';
                    $lastCategory = $currentCategory;
                }
                echo '</select>';
                ?>
            </td>
        </tr>
        <?php
        echo '<tr>';
        echo "<td class='right' colspan=2>";
        echo "<input type='hidden' name='q' value='".$_GET['q']."'>";
        echo "<a href='".$_SESSION[$guid]['absoluteURL']."/index.php?q=/modules/Badges/badges_grant.php'>".__('Clear Filters').'</a> ';
        echo "<input type='submit' value='".__('Go')."'>";
        echo '</td>';
        echo '</tr>';
        echo '</table>';
        echo '</form>';

        echo '<h3>';
        echo __('Badges');
        echo '</h3>';
        //Set pagination variable
        $page = 1;
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        }
        if ((!is_numeric($page)) or $page < 1) {
            $page = 1;
        }

        //Get gibbonHookID for link to Student Profile
        $gibbonHookID = null;
        try {
            $dataHook = array();
            $sqlHook = "SELECT gibbonHookID FROM gibbonHook WHERE name='Badges' AND type='Student Profile' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Badges')";
            $resultHook = $connection2->prepare($sqlHook);
            $resultHook->execute($dataHook);
        } catch (PDOException $e) {

        }
        if ($resultHook->rowCount() == 1) {
            $rowHook = $resultHook->fetch();
            $gibbonHookID = $rowHook['gibbonHookID'];
        }

        //Search with filters applied
        try {
            $data = array();
            $sqlWhere = 'AND ';
            if ($gibbonPersonID2 != '') {
                $data['gibbonPersonID'] = $gibbonPersonID2;
                $sqlWhere .= 'badgesBadgeStudent.gibbonPersonID=:gibbonPersonID AND ';
            }
            if ($badgesBadgeID2 != '') {
                $data['badgesBadgeID2'] = $badgesBadgeID2;
                $sqlWhere .= 'badgesBadge.badgesBadgeID=:badgesBadgeID2 AND ';
            }
            if ($sqlWhere == 'AND ') {
                $sqlWhere = '';
            } else {
                $sqlWhere = substr($sqlWhere, 0, -5);
            }
            $data['gibbonSchoolYearID2'] = $gibbonSchoolYearID;
            $sql = "SELECT badgesBadge.*, badgesBadgeStudent.*, surname, preferredName FROM badgesBadge JOIN badgesBadgeStudent ON (badgesBadgeStudent.badgesBadgeID=badgesBadge.badgesBadgeID) JOIN gibbonPerson ON (badgesBadgeStudent.gibbonPersonID=gibbonPerson.gibbonPersonID) WHERE badgesBadgeStudent.gibbonSchoolYearID=:gibbonSchoolYearID2 $sqlWhere ORDER BY timestamp DESC";
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            echo "<div class='error'>".$e->getMessage().'</div>';
        }
        $sqlPage = $sql.' LIMIT '.$_SESSION[$guid]['pagination'].' OFFSET '.(($page - 1) * $_SESSION[$guid]['pagination']);

        echo "<div class='linkTop'>";
        echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.$_SESSION[$guid]['module']."/badges_grant_add.php&gibbonPersonID2=$gibbonPersonID2&badgesBadgeID2=$badgesBadgeID2&gibbonSchoolYearID=$gibbonSchoolYearID'>".__('Add')."<img style='margin: 0 0 -4px 5px' title='".__('Add')."' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/page_new.png'/></a>";
        echo '</div>';

        if ($result->rowCount() < 1) {
            echo "<div class='error'>";
            echo __('There are no records to display.');
            echo '</div>';
        } else {
            if ($result->rowCount() > $_SESSION[$guid]['pagination']) {
                printPagination($guid, $result->rowCount(), $page, $_SESSION[$guid]['pagination'], 'top', "gibbonPersonID2=$gibbonPersonID2&badgesBadgeID2=$badgesBadgeID2&gibbonSchoolYearID=$gibbonSchoolYearID");
            }

            echo "<table cellspacing='0' style='width: 100%'>";
            echo "<tr class='head'>";
            echo "<th style='width: 180px'>";
            echo __('Badges');
            echo '</th>';
            echo '<th>';
            echo __('Student');
            echo '</th>';
            echo '<th>';
            echo __('Date');
            echo '</th>';
            echo "<th style='min-width: 70px'>";
            echo __('Actions');
            echo '</th>';
            echo '</tr>';

            $count = 0;
            $rowNum = 'odd';
            try {
                $resultPage = $connection2->prepare($sqlPage);
                $resultPage->execute($data);
            } catch (PDOException $e) {
                echo "<div class='error'>".$e->getMessage().'</div>';
            }
            while ($row = $resultPage->fetch()) {
                if ($count % 2 == 0) {
                    $rowNum = 'even';
                } else {
                    $rowNum = 'odd';
                }
                ++$count;

                //COLOR ROW BY STATUS!
                echo "<tr class=$rowNum>";
                echo "<td style='font-weight: bold; text-align: center'>";
                if ($row['logo'] != '') {
                    echo "<img class='user' style='margin-bottom: 10px; max-width: 150px' src='".$_SESSION[$guid]['absoluteURL'].'/'.$row['logo']."'/>";
                } else {
                    echo "<img class='user' style='margin-bottom: 10px; max-width: 150px' src='".$_SESSION[$guid]['absoluteURL'].'/themes/'.$_SESSION[$guid]['gibbonThemeName']."/img/anonymous_240_square.jpg'/>";
                }
                echo $row['name'];
                echo '</td>';
                echo '<td>';
                echo "<div style='padding: 2px 0px'><b><a href='index.php?q=/modules/Students/student_view_details.php&gibbonPersonID=".$row['gibbonPersonID']."&hook=Badges&module=Badges&action=View Badges_all&gibbonHookID=$gibbonHookID&search=&allStudents=&sort=surname, preferredName'>".formatName('', $row['preferredName'], $row['surname'], 'Student', true).'</a><br/></div>';
                echo '</td>';
                echo '<td>';
                echo dateConvertBack($guid, $row['date']).'<br/>';
                echo '</td>';
                echo '<td>';
                echo "<a class='thickbox' href='".$_SESSION[$guid]['absoluteURL'].'/fullscreen.php?q=/modules/'.$_SESSION[$guid]['module'].'/badges_grant_delete.php&badgesBadgeStudentID='.$row['badgesBadgeStudentID']."&gibbonPersonID2=$gibbonPersonID2&badgesBadgeID2=$badgesBadgeID2&gibbonSchoolYearID=$gibbonSchoolYearID&width=650&height=135'><img title='".__('Delete')."' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/garbage.png'/></a> ";
                echo "<script type='text/javascript'>";
                echo '$(document).ready(function(){';
                echo "\$(\".comment-$count\").hide();";
                echo "\$(\".show_hide-$count\").fadeIn(1000);";
                echo "\$(\".show_hide-$count\").click(function(){";
                echo "\$(\".comment-$count\").fadeToggle(1000);";
                echo '});';
                echo '});';
                echo '</script>';
                if ($row['comment'] != '') {
                    echo "<a title='".__('View Description')."' class='show_hide-$count' onclick='false' href='#'><img style='padding-right: 5px' src='".$_SESSION[$guid]['absoluteURL']."/themes/Default/img/page_down.png' alt='".__('Show Comment')."' onclick='return false;' /></a>";
                }
                echo '</td>';
                echo '</tr>';
                if ($row['comment'] != '') {
                    echo "<tr class='comment-$count' id='comment-$count'>";
                    echo '<td colspan=4>';
                    if ($row['comment'] != '') {
                        echo '<b>'.__('Comment').'</b><br/>';
                        echo nl2brr($row['comment']).'<br/><br/>';
                    }
                    echo '</td>';
                    echo '</tr>';
                }
            }
            echo '</table>';

            if ($result->rowCount() > $_SESSION[$guid]['pagination']) {
                printPagination($guid, $result->rowCount(), $page, $_SESSION[$guid]['pagination'], 'bottom', "gibbonPersonID2=$gibbonPersonID2&badgesBadgeID2=$badgesBadgeID2&gibbonSchoolYearID=$gibbonSchoolYearID");
            }
        }
    }
}
?>
