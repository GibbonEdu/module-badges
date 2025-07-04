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
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

include '../../gibbon.php';

include './moduleFunctions.php';

$badgesBadgeID = $_GET['badgesBadgeID'] ?? '';
$URL = $session->get('absoluteURL','').'/index.php?q=/modules/'.getModuleName($_POST['address'])."/badges_manage_edit.php&badgesBadgeID=$badgesBadgeID&search=".$_GET['search']."&category=".$_GET['category'];

if (isActionAccessible($guid, $connection2, '/modules/Badges/badges_manage_edit.php') == false) {
    //Fail 0
    $URL = $URL.'&return=error0';
    header("Location: {$URL}");
} else {
    //Proceed!
    //Check if school year specified
    if ($badgesBadgeID == '') {
        //Fail1
        $URL = $URL.'&return=error1';
        header("Location: {$URL}");
    } else {
        try {
            $data = array('badgesBadgeID' => $badgesBadgeID);
            $sql = 'SELECT * FROM badgesBadge WHERE badgesBadgeID=:badgesBadgeID';
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            //Fail2
            $URL = $URL.'&deleteReturn=error2';
            header("Location: {$URL}");
            exit();
        }

        if ($result->rowCount() != 1) {
            //Fail 2
            $URL = $URL.'&return=error2';
            header("Location: {$URL}");
        } else {
            $row = $result->fetch();

            //Validate Inputs
            $name = $_POST['name'] ?? '';
            $license = $_POST['license'] ?? '';
            $active = $_POST['active'] ?? '';
            $category = $_POST['category'] ?? '';
            $description = $_POST['description'] ?? '';
            $logoLicense = $_POST['logoLicense'] ?? '';

            if ($name == '' or $license == '' or $active == '' or $category == '') {
                //Fail 3
                $URL = $URL.'&return=error3';
                header("Location: {$URL}");
            } else {
                //Sort out logo
                $partialFail = false;
                $logo = $_POST['logo'] ?? $row['logo'];
                if (!empty($_FILES['file']['tmp_name'])) {
                    $fileUploader = new Gibbon\FileUploader($pdo, $session);
                    $fileUploader->getFileExtensions('Graphics/Design');

                    $file = (isset($_FILES['file']))? $_FILES['file'] : null;

                    // Upload the file, return the /uploads relative path
                    $logo = $fileUploader->uploadFromPost($file, $name);

                    if (empty($logo)) {
                        $partialFail = true;
                    }
                }

                //Write to database
                try {
                    $data = array('name' => $name, 'license' => $license, 'active' => $active, 'category' => $category, 'description' => $description, 'logo' => $logo, 'logoLicense' => $logoLicense, 'badgesBadgeID' => $badgesBadgeID);
                    $sql = 'UPDATE badgesBadge SET name=:name, license=:license, active=:active, category=:category, description=:description, logo=:logo, logoLicense=:logoLicense WHERE badgesBadgeID=:badgesBadgeID';
                    $result = $connection2->prepare($sql);
                    $result->execute($data);
                } catch (PDOException $e) {
                    //Fail 2
                    $URL = $URL.'&return=error5';
                    header("Location: {$URL}");
                    exit();
                }

                if ($partialFail == true) {
                    $URL .= '&return=warning1';
                    header("Location: {$URL}");
                } else {
                    $URL .= "&return=success0";
                    header("Location: {$URL}");
                }
            }
        }
    }
}
