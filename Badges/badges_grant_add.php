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
use Gibbon\Forms\DatabaseFormFactory;

//Module includes
include './modules/'.$_SESSION[$guid]['module'].'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Badges/badges_grant_add.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo __('You do not have access to this action.');
    echo '</div>';
} else {
    //Proceed!
    $page->breadcrumbs
        ->add(__('Grant Badges'), 'badges_grant.php')
        ->add(__('Add'));

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    $gibbonSchoolYearID = $_GET['gibbonSchoolYearID'] ?? '';

    echo "<div class='linkTop'>";
    if ($_GET['gibbonPersonID2'] != '' or $_GET['badgesBadgeID2'] != '') { echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Badges/badges_grant.php&gibbonPersonID2='.$_GET['gibbonPersonID2'].'&badgesBadgeID2='.$_GET['badgesBadgeID2']."'>".__('Back to Search Results').'</a>';
    }
    echo '</div>';

    $form = Form::create('grantBadges', $_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module'].'/badges_grant_addProcess.php?gibbonPersonID2='.$_GET['gibbonPersonID2'].'&badgesBadgeID2='.$_GET['badgesBadgeID2']."&gibbonSchoolYearID=$gibbonSchoolYearID");

    $form->setFactory(DatabaseFormFactory::create($pdo));
            
    $form->addHiddenValue('address', $_SESSION[$guid]['address']);
    $form->addHiddenValue('gibbonSchoolYearID', $_SESSION[$guid]['gibbonSchoolYearID']);

    $row = $form->addRow();
        $row->addLabel('gibbonPersonIDMulti', __('Students'));
        $row->addSelectUsers('gibbonPersonIDMulti', $_SESSION[$guid]['gibbonSchoolYearID'], ['includeStudents' => true])->selectMultiple()->isRequired();

    $sql = "SELECT badgesBadgeID as value, name, category FROM badgesBadge WHERE active='Y' ORDER BY category, name";
    $row = $form->addRow();
        $row->addLabel('badgesBadgeID', __('Badge'));
        $row->addSelect('badgesBadgeID')->fromQuery($pdo, $sql, [], 'category')->isRequired()->placeholder();

    $row = $form->addRow();
        $row->addLabel('date', __('Date'));
        $row->addDate('date')->setValue(date($_SESSION[$guid]['i18n']['dateFormatPHP']))->isRequired();

    $col = $form->addRow()->addColumn();
        $col->addLabel('comment', __('Comment'));
        $col->addTextArea('comment')->setRows(8)->setClass('w-full');

    $row = $form->addRow();
        $row->addSubmit();

    echo $form->getOutput();
}
