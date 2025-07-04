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

use Gibbon\Services\Format;

include '../../gibbon.php';

$gibbonSchoolYearID = $_POST['gibbonSchoolYearID'] ?? '';

$URL = $session->get('absoluteURL','').'/index.php?q=/modules/'.getModuleName($_POST['address']).'/badges_grant_add.php&gibbonPersonID2='.$_GET['gibbonPersonID2'].'&badgesBadgeID2='.$_GET['badgesBadgeID2']."&gibbonSchoolYearID=$gibbonSchoolYearID";

if (isActionAccessible($guid, $connection2, '/modules/Badges/badges_grant_add.php') == false) {
    //Fail 0
    $URL .= '&return=error0';
    header("Location: {$URL}");
} else {
    //Proceed!
    $gibbonPersonIDMulti = $_POST['gibbonPersonIDMulti'] ?? null;
    $badgesBadgeID = $_POST['badgesBadgeID'] ?? '';
    $date = $_POST['date'] ?? '';
    $comment = $_POST['comment'] ?? '';

    if ($gibbonPersonIDMulti == null or $date == '' or $badgesBadgeID == '' or $gibbonSchoolYearID == '') {
        //Fail 3
        $URL .= '&return=error3';
        header("Location: {$URL}");
    } else {
        $partialFail = false;

        foreach ($gibbonPersonIDMulti as $gibbonPersonID) {
            //Write to database
            try {
                $data = array('badgesBadgeID' => $badgesBadgeID, 'gibbonSchoolYearID' => $gibbonSchoolYearID, 'date' => Format::dateConvert($date), 'gibbonPersonID' => $gibbonPersonID, 'comment' => $comment, 'gibbonPersonIDCreator' => $session->get('gibbonPersonID',''));
                $sql = 'INSERT INTO badgesBadgeStudent SET badgesBadgeID=:badgesBadgeID, gibbonSchoolYearID=:gibbonSchoolYearID, date=:date, gibbonPersonID=:gibbonPersonID, comment=:comment, gibbonPersonIDCreator=:gibbonPersonIDCreator';
                $result = $connection2->prepare($sql);
                $result->execute($data);
            } catch (PDOException $e) {
                $partialFail = true;
            }

            $badgesBadgeStudentID = $connection2->lastInsertID();

            //Notify User
            $notificationGateway = new \Gibbon\Domain\System\NotificationGateway($pdo);
			$notificationSender = new \Gibbon\Comms\NotificationSender($notificationGateway, $session);
			$notificationText = __m('Someone has granted you a badge.');
			$notificationSender->addNotification($gibbonPersonID, $notificationText, 'Badges', "/index.php?q=/modules/Badges/badges_view.php&gibbonPersonID=$gibbonPersonID");
			$notificationSender->sendNotifications();
        }

        if ($partialFail == true) {
            //Fail 5
            $URL .= '&return=error5';
            header("Location: {$URL}");
        } else {
            //Success 0
            $URL .= '&return=success0';
            header("Location: {$URL}");
        }
    }
}
