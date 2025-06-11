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

use Gibbon\Forms\Form;
use Gibbon\Services\Format;
use Gibbon\Forms\DatabaseFormFactory;

//Module includes
include './modules/'.$session->get('module').'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Badges/badges_view.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo __('You do not have access to this action.');
    echo '</div>';
} else {
    //Proceed!
    $page->breadcrumbs->add(__('View Badges'));

    //Get action with highest precendence
    $highestAction = getHighestGroupedAction($guid, $_GET['q'], $connection2);
    if ($highestAction == false) { echo "<div class='error'>";
        echo __('The highest grouped action cannot be determined.');
        echo '</div>';
    } else {
        if ($highestAction == 'View Badges_all') {
            $gibbonPersonID = $_GET['gibbonPersonID'] ?? '';

            $form = Form::create('search', $session->get('absoluteURL','').'/index.php', 'GET');
            $form->setTitle(__('Choose Student'));
            $form->addClass('noIntBorder');
            $form->setFactory(DatabaseFormFactory::create($pdo));

            $form->addHiddenValue('q', '/modules/'.$session->get('module').'/badges_view.php');

            $row = $form->addRow();
                $row->addLabel('gibbonPersonID', __('Student'));
                $row->addSelectStudent('gibbonPersonID', $session->get('gibbonSchoolYearID'))->placeholder()->selected($gibbonPersonID);

            $row = $form->addRow();
                $row->addSearchSubmit($session);

            echo $form->getOutput();

            if ($gibbonPersonID != '') {
                $output = '';
                echo '<h2>';
                echo __('Badges');
                echo '</h2>';

                try {
                    $data = array('gibbonPersonID' => $gibbonPersonID);
                    $sql = 'SELECT * FROM gibbonPerson WHERE gibbonPerson.gibbonPersonID=:gibbonPersonID ORDER BY surname, preferredName';
                    $result = $connection2->prepare($sql);
                    $result->execute($data);
                } catch (PDOException $e) {
                    echo "<div class='error'>".$e->getMessage().'</div>';
                }
                if ($result->rowCount() != 1) {
                    echo "<div class='error'>";
                    echo __('The specified record does not exist.');
                    echo '</div>';
                } else {
                    echo getBadges($connection2, $guid, $gibbonPersonID);
                }
            }
        } elseif ($highestAction == 'View Badges_my') {
            $output = '';
            echo '<h2>';
            echo __('My Badges');
            echo '</h2>';

            try {
                $data = array('gibbonPersonID' => $session->get('gibbonPersonID'));
                $sql = 'SELECT * FROM gibbonPerson WHERE gibbonPerson.gibbonPersonID=:gibbonPersonID ORDER BY surname, preferredName';
                $result = $connection2->prepare($sql);
                $result->execute($data);
            } catch (PDOException $e) {
                echo "<div class='error'>".$e->getMessage().'</div>';
            }
            if ($result->rowCount() != 1) {
                echo "<div class='error'>";
                echo __('The specified record does not exist.');
                echo '</div>';
            } else {
                echo getBadges($connection2, $guid, $session->get('gibbonPersonID'));
            }
        } elseif ($highestAction == 'View Badges_myChildren') {
            $gibbonPersonID = $_GET['search'] ?? $session->get('gibbonPersonID');

            //Test data access field for permission
            try {
                $data = array('gibbonPersonID' => $session->get('gibbonPersonID'));
                $sql = "SELECT * FROM gibbonFamilyAdult WHERE gibbonPersonID=:gibbonPersonID AND childDataAccess='Y'";
                $result = $connection2->prepare($sql);
                $result->execute($data);
            } catch (PDOException $e) {
                echo "<div class='error'>".$e->getMessage().'</div>';
            }
            if ($result->rowCount() < 1) {
                $page->addMessage(__('There are no records to display.'));
            } else {
                //Get child list
                $count = 0;
                $users = array(
                    $session->get('gibbonPersonID') => Format::name('', $session->get('preferredName'), $session->get('surname'), 'Student', true)
                );
                while ($row = $result->fetch()) {
                    try {
                        $dataChild = array('gibbonFamilyID' => $row['gibbonFamilyID'], 'gibbonSchoolYearID' => $session->get('gibbonSchoolYearID'));
                        $sqlChild = "SELECT * FROM gibbonFamilyChild JOIN gibbonPerson ON (gibbonFamilyChild.gibbonPersonID=gibbonPerson.gibbonPersonID) JOIN gibbonStudentEnrolment ON (gibbonPerson.gibbonPersonID=gibbonStudentEnrolment.gibbonPersonID) JOIN gibbonFormGroup ON (gibbonStudentEnrolment.gibbonFormGroupID=gibbonFormGroup.gibbonFormGroupID) WHERE gibbonFamilyID=:gibbonFamilyID AND gibbonPerson.status='Full' AND (dateStart IS NULL OR dateStart<='".date('Y-m-d')."') AND (dateEnd IS NULL  OR dateEnd>='".date('Y-m-d')."') AND gibbonStudentEnrolment.gibbonSchoolYearID=:gibbonSchoolYearID ORDER BY surname, preferredName ";
                        $resultChild = $connection2->prepare($sqlChild);
                        $resultChild->execute($dataChild);
                    } catch (PDOException $e) {
                        echo "<div class='error'>".$e->getMessage().'</div>';
                    }

                    while ($rowChild = $resultChild->fetch()) {
                        $users[$rowChild['gibbonPersonID']] = Format::name('', $rowChild['preferredName'], $rowChild['surname'], 'Student', true);
                        $count ++;
                    }
                }

                echo '<h2>';
                echo __('Choose');
                echo '</h2>';

                $form = Form::create('action', $session->get('absoluteURL','')."/index.php", "get");
                $form->setClass('noIntBorder fullWidth');

                $form->addHiddenValue('address', "/modules/".$session->get('module')."/badges_View.php");
                $form->addHiddenValue('q', $session->get('address'));

                $row = $form->addRow();
                    $row->addLabel('search', __('User'));
                    $row->addSelect('search')->fromArray($users)->selected($gibbonPersonID);

                $row = $form->addRow();
                    $row->addSearchSubmit($session);

                echo $form->getOutput();


                if ($gibbonPersonID != '' and $count > 0) {
                    //Confirm access to this student
                    try {
                        $dataChild = array('gibbonPersonID' => $gibbonPersonID, 'gibbonPersonID2' => $session->get('gibbonPersonID'), 'gibbonPersonID3' => $session->get('gibbonPersonID'));
                        $sqlChild = "(SELECT gibbonPerson.gibbonPersonID FROM gibbonFamilyChild JOIN gibbonFamily ON (gibbonFamilyChild.gibbonFamilyID=gibbonFamily.gibbonFamilyID) JOIN gibbonFamilyAdult ON (gibbonFamilyAdult.gibbonFamilyID=gibbonFamily.gibbonFamilyID) JOIN gibbonPerson ON (gibbonFamilyChild.gibbonPersonID=gibbonPerson.gibbonPersonID) WHERE gibbonPerson.status='Full' AND (dateStart IS NULL OR dateStart<='".date('Y-m-d')."') AND (dateEnd IS NULL  OR dateEnd>='".date('Y-m-d')."') AND gibbonFamilyChild.gibbonPersonID=:gibbonPersonID AND gibbonFamilyAdult.gibbonPersonID=:gibbonPersonID2 AND childDataAccess='Y')
                            UNION
                            (SELECT gibbonPersonID FROM gibbonPerson WHERE gibbonPersonID=:gibbonPersonID3)
                        ";
                        $resultChild = $connection2->prepare($sqlChild);
                        @$resultChild->execute($dataChild);
                    } catch (PDOException $e) {
                        echo "<div class='error'>".$e->getMessage().'</div>';
                    }

                    if ($resultChild->rowCount() < 1) {
                        echo "<div class='error'>";
                        echo __('The selected record does not exist, or you do not have access to it.');
                        echo '</div>';
                    } else {
                        $rowChild = $resultChild->fetch();

                        if ($gibbonPersonID != '') {
                            $output = '';
                            echo '<h2>';
                            echo __('Badges');
                            echo '</h2>';

                            try {
                                $data = array('gibbonPersonID' => $gibbonPersonID);
                                $sql = 'SELECT * FROM gibbonPerson WHERE gibbonPerson.gibbonPersonID=:gibbonPersonID ORDER BY surname, preferredName';
                                $result = $connection2->prepare($sql);
                                $result->execute($data);
                            } catch (PDOException $e) {
                                echo "<div class='error'>".$e->getMessage().'</div>';
                            }
                            if ($result->rowCount() != 1) {
                                echo "<div class='error'>";
                                echo __('The specified record does not exist.');
                                echo '</div>';
                            } else {
                                $row = $result->fetch();
                                echo getBadges($connection2, $guid, $gibbonPersonID);
                            }
                        }
                    }
                }
            }
        }
    }
}
?>
