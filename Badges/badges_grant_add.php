<?php
/*
Gibbon: the flexible, open school platform
Founded by Ross Parker at ICHK Secondary. Built by Ross Parker, Sandra Kuipers and the Gibbon community (https://gibbonedu.org/about/)
Copyright © 2010, Gibbon Foundation
Gibbon™, Gibbon Education Ltd. (Hong Kong)

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

use Gibbon\Http\Url;
use Gibbon\Forms\Form;
use Gibbon\Forms\DatabaseFormFactory;

//Module includes
include './modules/'.$session->get('module').'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Badges/badges_grant_add.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo __('You do not have access to this action.');
    echo '</div>';
} else {
    //Proceed!
    $gibbonSchoolYearID = $_GET['gibbonSchoolYearID'] ?? '';

    $page->breadcrumbs
        ->add(__('Grant Badges'), 'badges_grant.php&gibbonSchoolYearID='.$gibbonSchoolYearID)
        ->add(__('Add'));

    $gibbonPersonID2 = $_GET['gibbonPersonID2'] ?? '';
    $badgesBadgeID2 = $_GET['badgesBadgeID2'] ?? '';
    if (!empty($gibbonPersonID2) || !empty($badgesBadgeID2)) {
        $params = [
            "gibbonPersonID2" => $gibbonPersonID2,
            "badgesBadgeID2" => $badgesBadgeID2
        ];
        $page->navigator->addSearchResultsAction(Url::fromModuleRoute('Badges', 'badges_grant.php')->withQueryParams($params));
    }

    $form = Form::create('grantBadges', $session->get('absoluteURL').'/modules/'.$session->get('module')."/badges_grant_addProcess.php?gibbonPersonID2=$gibbonPersonID2&badgesBadgeID2=$badgesBadgeID2&gibbonSchoolYearID=$gibbonSchoolYearID");

    $form->setFactory(DatabaseFormFactory::create($pdo));

    $form->addHiddenValue('address', $session->get('address'));
    $form->addHiddenValue('gibbonSchoolYearID', $gibbonSchoolYearID);

    $row = $form->addRow();
        $row->addLabel('gibbonPersonIDMulti', __('Students'));
        $row->addSelectUsers('gibbonPersonIDMulti', $session->get('gibbonSchoolYearID'), ['includeStudents' => true])->selectMultiple()->isRequired();

    $sql = "SELECT badgesBadgeID as value, name, category FROM badgesBadge WHERE active='Y' ORDER BY category, name";
    $row = $form->addRow();
        $row->addLabel('badgesBadgeID', __('Badge'));
        $row->addSelect('badgesBadgeID')->fromQuery($pdo, $sql, [], 'category')->isRequired()->placeholder();

    $row = $form->addRow();
        $row->addLabel('date', __('Date'));
        $row->addDate('date')->setValue(date($session->get('i18n')['dateFormatPHP']))->isRequired();

    $col = $form->addRow()->addColumn();
        $col->addLabel('comment', __('Comment'));
        $col->addTextArea('comment')->setRows(8)->setClass('w-full');

    $row = $form->addRow();
        $row->addSubmit();

    echo $form->getOutput();
}
