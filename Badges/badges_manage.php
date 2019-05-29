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
use Gibbon\Forms\Form;

//Module includes
include './modules/Badges/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Badges/badges_manage.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    //Proceed!
    $page->breadcrumbs->add(__('Manage Badges'));

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    //Set pagination variable
    $page = null;
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    }
    if ((!is_numeric($page)) or $page < 1) {
        $page = 1;
    }

    //Build role lookup array
    $allRoles = array();
    try {
        $dataRoles = array();
        $sqlRoles = 'SELECT * FROM gibbonRole';
        $resultRoles = $connection2->prepare($sqlRoles);
        $resultRoles->execute($dataRoles);
    } catch (PDOException $e) {

    }
    while ($rowRoles = $resultRoles->fetch()) {
        $allRoles[$rowRoles['gibbonRoleID']] = $rowRoles['name'];
    }

    $search = $_GET['search'] ?? '';
    $category = $_GET['category'] ?? '';

    $form = Form::create('search', $gibbon->session->get('absoluteURL','').'/index.php', 'get');
    $form->setTitle(__('Search & Filter'));
    $form->addClass('noIntBorder');

    $form->addHiddenValue('q', '/modules/'.$gibbon->session->get('module').'/badges_manage.php');
    $form->addHiddenValue('address', '/modules/' . $gibbon->session->get('address'));

    $row = $form->addRow();
        $row->addLabel('search', __('Search For'))->description(__('Name'));
        $row->addTextField('search')->setValue($search);

    $categories = getSettingByScope($connection2, 'Badges', 'badgeCategories');
    $categories = !empty($categories) ? array_map('trim', explode(',', $categories)) : [];
    $row = $form->addRow();
        $row->addLabel('category', __('Category'));
        $row->addSelect('category')->fromArray($categories)->selected($category)->placeholder();

    $row = $form->addRow();
        $row->addSearchSubmit($gibbon->session, __('Clear Search'));

    echo $form->getOutput();

    echo "<h2 class='top'>";
    echo __('View');
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
        $sqlPage = $sql . ' LIMIT ' . $gibbon->session->get('pagination') . ' OFFSET ' . (($page - 1) * $gibbon->session->get('pagination'));
        $result = $connection2->prepare($sql);
        $result->execute($data);
    } catch (PDOException $e) {
        echo "<div class='error'>" . $e->getMessage() . '</div>';
    }

    echo "<div class='linkTop'>";
    echo "<a href='" . $gibbon->session->get('absoluteURL','') . "/index.php?q=/modules/Badges/badges_manage_add.php&search=$search&category=$category'>" . __('Add') . "<img style='margin-left: 5px' title='" . __('Add') . "' src='./themes/" . $gibbon->session->get('gibbonThemeName') . "/img/page_new.png'/></a>";
    echo '</div>';

    if ($result->rowCount() < 1) {
        echo "<div class='error'>";
        echo 'There are no badges to display.';
        echo '</div>';
    } else {
        if ($result->rowCount() > $gibbon->session->get('pagination')) {
            printPagination($guid, $result->rowCount(), $page, $gibbon->session->get('pagination'), 'top', "search=$search");
        }

        echo "<table cellspacing='0' style='width: 100%'>";
        echo "<tr class='head'>";
        echo "<th style='width: 180px'>";
        echo __('Logo');
        echo '</th>';
        echo '<th>';
        echo 'Name<br/>';
        echo '</th>';
        echo '<th>';
        echo 'License<br/>';
        echo '</th>';
        echo '<th>';
        echo 'Category';
        echo '</th>';
        echo "<th style='width: 120px'>";
        echo 'Actions';
        echo '</th>';
        echo '</tr>';

        $count = 0;
        $rowNum = 'odd';
        try {
            $resultPage = $connection2->prepare($sqlPage);
            $resultPage->execute($data);
        } catch (PDOException $e) {
            echo "<div class='error'>" . $e->getMessage() . '</div>';
        }
        while ($row = $resultPage->fetch()) {
            if ($count % 2 == 0) {
                $rowNum = 'even';
            } else {
                $rowNum = 'odd';
            }
            ++$count;

            if ($row['active'] == 'N') {
                $rowNum = 'error';
            }

            //COLOR ROW BY STATUS!
            echo "<tr class=$rowNum>";
            echo '<td>';
            if ($row['logo'] != '') {
                echo "<img class='user' style='max-width: 150px' src='" . $gibbon->session->get('absoluteURL','') . '/' . $row['logo'] . "'/>";
            } else {
                echo "<img class='user' style='max-width: 150px' src='" . $gibbon->session->get('absoluteURL','') . '/themes/' . $gibbon->session->get('gibbonThemeName') . "/img/anonymous_240_square.jpg'/>";
            }
            echo '</td>';
            echo '<td>';
            echo $row['name'];
            echo '</td>';
            echo '<td>';
            if ($row['license'] == "Y") {
                echo "<img title='" . __('Yes'). "' src='../themes/Default/img/iconTick.png' />";
            }
            echo '</td>';
            echo '<td>';
            echo $row['category'];
            echo '</td>';
            echo '<td>';
            echo "<script type='text/javascript'>";
            echo '$(document).ready(function(){';
            echo "\$(\".comment-$count\").hide();";
            echo "\$(\".show_hide-$count\").fadeIn(1000);";
            echo "\$(\".show_hide-$count\").click(function(){";
            echo "\$(\".comment-$count\").fadeToggle(1000);";
            echo '});';
            echo '});';
            echo '</script>';
            echo "<a href='" . $gibbon->session->get('absoluteURL','') . '/index.php?q=/modules/Badges/badges_manage_edit.php&badgesBadgeID=' . $row['badgesBadgeID'] . "&search=$search&category=$category'><img title='Edit' src='./themes/" . $gibbon->session->get('gibbonThemeName') . "/img/config.png'/></a> ";
            echo "<a class='thickbox' href='" . $gibbon->session->get('absoluteURL','') . '/fullscreen.php?q=/modules/Badges/badges_manage_delete.php&badgesBadgeID=' . $row['badgesBadgeID'] . "&search=$search&category=$category&width=650&height=135'><img title='Delete' src='./themes/" . $gibbon->session->get('gibbonThemeName') . "/img/garbage.png'/></a> ";
            if ($row['description'] != '') {
                echo "<a class='show_hide-$count' onclick='false' href='#'><img style='padding-right: 5px' src='" . $gibbon->session->get('absoluteURL','') . "/themes/Default/img/page_down.png' title='Show Description' onclick='return false;' /></a>";
            }
            echo '</td>';
            echo '</tr>';
            if ($row['description'] != '') {
                echo "<tr class='comment-$count' id='comment-$count'>";
                echo "<td style='background-color: #fff' colspan=5>";
                echo nl2brr($row['description']);
                echo '</td>';
                echo '</tr>';
            }
        }
        echo '</table>';

        if ($result->rowCount() > $gibbon->session->get('pagination')) {
            printPagination($guid, $result->rowCount(), $page, $gibbon->session->get('pagination'), 'bottom', "search=$search");
        }
    }
}
?>
