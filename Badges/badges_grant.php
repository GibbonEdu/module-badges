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
use Gibbon\Tables\DataTable;
use Gibbon\Forms\DatabaseFormFactory;
use Gibbon\Module\Badges\Domain\BadgeGateway;

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

    //Get gibbonHookID for link to Student Profile
    $gibbonHookID = null;
    $dataHook = array();
    $sqlHook = "SELECT gibbonHookID FROM gibbonHook WHERE name='Badges' AND type='Student Profile' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Badges')";
    $resultHook = $connection2->prepare($sqlHook);
    $resultHook->execute($dataHook);
    if ($resultHook->rowCount() == 1) {
        $rowHook = $resultHook->fetch();
        $gibbonHookID = $rowHook['gibbonHookID'];
    }

    if ($gibbonSchoolYearID != '') {
        $page->navigator->addSchoolYearNavigation($gibbonSchoolYearID);

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

        $badgeGateway = $container->get(BadgeGateway::class);

        // QUERY
        $criteria = $badgeGateway->newQueryCriteria(true)
            ->sortBy(['timestamp'], 'DESC')
            ->pageSize(50)
            ->fromPOST();

        $badges = $badgeGateway->queryBadgeGrants($criteria, $gibbonSchoolYearID, $gibbonPersonID2, $badgesBadgeID2);

        // TABLE
        $table = DataTable::createPaginated('badges', $criteria);
        $table->setTitle(__('View'));

        $table->addHeaderAction('add', __('Add'))
            ->addParam('gibbonPersonID2', $gibbonPersonID2)
            ->addParam('badgesBadgeID2', $badgesBadgeID2)
            ->addParam('gibbonSchoolYearID', $gibbonSchoolYearID)
            ->setURL('/modules/Badges/badges_grant_add.php')
            ->displayLabel();

        $table->addExpandableColumn('comment')->format(function ($values) use ($session) {
            $return = '';

            if (!empty($values['description'])) {
                $return .= Format::bold(__('Description'))."<br/>".$values['description']."<br/>";
            }
            if (!empty($values['comment'])) {
                if (!empty($values['description'])) {
                    $return .= "<br/>";
                }
                $return .= Format::bold(__('Comment'))."<br/>".$values['comment'];
            }

            return $return;
        });

        $table->addColumn('badge', __('Badge'))
            ->format(function ($values) use ($session) {
                $return = "<div class='text-center'>";
                if ($values['logo'] != '') {
                    $return .= "<img class='user' style='max-width: 150px' src='" . $session->get('absoluteURL','') . '/' . $values['logo'] . "'/>";
                } else {
                    $return .= "<img class='user' style='max-width: 150px' src='" . $session->get('absoluteURL','') . '/themes/' . $session->get('gibbonThemeName') . "/img/anonymous_240_square.jpg'/>";
                }
                $return .= "<br/>".Format::bold($values['name'])."</div>";

                return $return;
            });

        $table->addColumn('student', __('Student'))
            ->format(function ($values) use ($gibbonHookID) {
                return "<a href='index.php?q=/modules/Students/student_view_details.php&gibbonPersonID=".$values['gibbonPersonID']."&hook=Badges&module=Badges&action=View Badges_all&gibbonHookID=$gibbonHookID&search=&allStudents=&sort=surname, preferredName'>".Format::name('', $values['preferredName'], $values['surname'], 'Student', true).'</a>';
            });

        $table->addColumn('date', __('Date'))->format(Format::using('date', 'date'));

        $actions = $table->addActionColumn()
            ->addParam('badgesBadgeStudentID')
            ->addParam('gibbonPersonID2', $gibbonPersonID2)
            ->addParam('badgesBadgeID2', $badgesBadgeID2)
            ->addParam('gibbonSchoolYearID', $gibbonSchoolYearID)
            ->format(function ($resource, $actions) {
                $actions->addAction('delete', __('Delete'))
                    ->setURL('/modules/Badges/badges_grant_delete.php');
            });

        echo $table->render($badges);
    }
}
?>
