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

use Gibbon\Forms\Form;
use Gibbon\Services\Format;
use Gibbon\Forms\DatabaseFormFactory;

//Module includes
include './modules/'.$gibbon->session->get('module').'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Badges/badges_grant.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo __('You do not have access to this action.');
    echo '</div>';
} else {
    //Get action with highest precendence
    $page->breadcrumbs->add(__('Grant Badges'));

    $gibbonSchoolYearID = $_GET['gibbonSchoolYearID'] ?? $gibbon->session->get('gibbonSchoolYearID');
    if (isset($_GET['gibbonSchoolYearID'])) {

    }
    if ($gibbonSchoolYearID == $gibbon->session->get('gibbonSchoolYearID')) {
        $gibbonSchoolYearName = $gibbon->session->get('gibbonSchoolYearName');
    }

    if ($gibbonSchoolYearID != $gibbon->session->get('gibbonSchoolYearID')) {
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
            echo "<a href='".$gibbon->session->get('absoluteURL').'/index.php?q=/modules/'.$gibbon->session->get('module').'/badges_grant.php&gibbonSchoolYearID='.getPreviousSchoolYearID($gibbonSchoolYearID, $connection2)."'>".__('Previous Year').'</a> ';
        } else {
            echo __('Previous Year').' ';
        }
        echo ' | ';
        if (getNextSchoolYearID($gibbonSchoolYearID, $connection2) != false) {
            echo "<a href='".$gibbon->session->get('absoluteURL').'/index.php?q=/modules/'.$gibbon->session->get('module').'/badges_grant.php&gibbonSchoolYearID='.getNextSchoolYearID($gibbonSchoolYearID, $connection2)."'>".__('Next Year').'</a> ';
        } else {
            echo __('Next Year').' ';
        }
        echo '</div>';

        $gibbonPersonID2 = $_GET['gibbonPersonID2'] ?? '';
        $badgesBadgeID2 = $_GET['badgesBadgeID2'] ?? '';
        $gibbonYearGroupID = $_GET['gibbonYearGroupID'] ?? '';
        $type = $_GET['type'] ?? '';

        $form = Form::create('grantbadges',$gibbon->session->get('absoluteURL').'/index.php?q=/modules/Badges/badges_grant.php','GET');
        $form->setFactory(DatabaseFormFactory::create($pdo));
        $form->addClass('noIntBorder');

        $form->setTitle(__('Filter'));
        $form->addRow();

        $row = $form->addRow();
        $row->addLabel('gibbonPersonID2',__('User'));
        $row->addSelectStudent('gibbonPersonID2', $gibbon->session->get('gibbonSchoolYearID'))->selected($gibbonPersonID2)->placeholder();

        $sql = "SELECT badgesBadgeID as value, name, category FROM badgesBadge WHERE active='Y' ORDER BY category, name";
        $row = $form->addRow();
        $row->addLabel('badgesBadgeID2',__('Badges'));
        $row->addSelect('badgesBadgeID2')->fromQuery($pdo, $sql, [], 'category')->selected($badgesBadgeID2)->placeholder();

        $row = $form->addRow();
        $row->addSearchSubmit($gibbon->session);

        $form->addHiddenValue('q',$_GET['q']);
        $form->addRow();
        echo $form->getOutput();
        ?>


        <?php


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
            $sqlWhere = $sqlWhere == 'AND ' ? '' : substr($sqlWhere, 0, -5);

            $data['gibbonSchoolYearID2'] = $gibbonSchoolYearID;
            $sql = "SELECT badgesBadge.*, badgesBadgeStudent.*, surname, preferredName FROM badgesBadge JOIN badgesBadgeStudent ON (badgesBadgeStudent.badgesBadgeID=badgesBadge.badgesBadgeID) JOIN gibbonPerson ON (badgesBadgeStudent.gibbonPersonID=gibbonPerson.gibbonPersonID) WHERE badgesBadgeStudent.gibbonSchoolYearID=:gibbonSchoolYearID2 $sqlWhere ORDER BY timestamp DESC";
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            echo "<div class='error'>".$e->getMessage().'</div>';
        }
        $sqlPage = $sql.' LIMIT '.$gibbon->session->get('pagination').' OFFSET '.(($page - 1) * $gibbon->session->get('pagination'));

        echo "<div class='linkTop'>";
        echo "<a href='".$gibbon->session->get('absoluteURL').'/index.php?q=/modules/'.$gibbon->session->get('module')."/badges_grant_add.php&gibbonPersonID2=$gibbonPersonID2&badgesBadgeID2=$badgesBadgeID2&gibbonSchoolYearID=$gibbonSchoolYearID'>".__('Add')."<img style='margin: 0 0 -4px 5px' title='".__('Add')."' src='./themes/".$gibbon->session->get('gibbonThemeName')."/img/page_new.png'/></a>";
        echo '</div>';

        if ($result->rowCount() < 1) {
            echo "<div class='error'>";
            echo __('There are no records to display.');
            echo '</div>';
        } else {
            if ($result->rowCount() > $gibbon->session->get('pagination')) {
                printPagination($guid, $result->rowCount(), $page, $gibbon->session->get('pagination'), 'top', "gibbonPersonID2=$gibbonPersonID2&badgesBadgeID2=$badgesBadgeID2&gibbonSchoolYearID=$gibbonSchoolYearID");
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
                    echo "<img class='user' style='margin-bottom: 10px; max-width: 150px' src='".$gibbon->session->get('absoluteURL').'/'.$row['logo']."'/>";
                } else {
                    echo "<img class='user' style='margin-bottom: 10px; max-width: 150px' src='".$gibbon->session->get('absoluteURL').'/themes/'.$gibbon->session->get('gibbonThemeName')."/img/anonymous_240_square.jpg'/>";
                }
                echo $row['name'];
                echo '</td>';
                echo '<td>';
                echo "<div style='padding: 2px 0px'><b><a href='index.php?q=/modules/Students/student_view_details.php&gibbonPersonID=".$row['gibbonPersonID']."&hook=Badges&module=Badges&action=View Badges_all&gibbonHookID=$gibbonHookID&search=&allStudents=&sort=surname, preferredName'>".Format::name('', $row['preferredName'], $row['surname'], 'Student', true).'</a><br/></div>';
                echo '</td>';
                echo '<td>';
                echo Format::date($row['date']).'<br/>';
                echo '</td>';
                echo '<td>';
                echo "<a class='thickbox' href='".$gibbon->session->get('absoluteURL').'/fullscreen.php?q=/modules/'.$gibbon->session->get('module').'/badges_grant_delete.php&badgesBadgeStudentID='.$row['badgesBadgeStudentID']."&gibbonPersonID2=$gibbonPersonID2&badgesBadgeID2=$badgesBadgeID2&gibbonSchoolYearID=$gibbonSchoolYearID&width=650&height=135'><img title='".__('Delete')."' src='./themes/".$gibbon->session->get('gibbonThemeName')."/img/garbage.png'/></a> ";
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
                    echo "<a title='".__('View Description')."' class='show_hide-$count' onclick='false' href='#'><img style='padding-right: 5px' src='".$gibbon->session->get('absoluteURL')."/themes/Default/img/page_down.png' alt='".__('Show Comment')."' onclick='return false;' /></a>";
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

            if ($result->rowCount() > $gibbon->session->get('pagination')) {
                printPagination($guid, $result->rowCount(), $page, $gibbon->session->get('pagination'), 'bottom', "gibbonPersonID2=$gibbonPersonID2&badgesBadgeID2=$badgesBadgeID2&gibbonSchoolYearID=$gibbonSchoolYearID");
            }
        }
    }
}
?>
