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

include '../../functions.php';
include '../../config.php';

include './moduleFunctions.php';

//New PDO DB connection
try {
    $connection2 = new PDO("mysql:host=$databaseServer;dbname=$databaseName", $databaseUsername, $databasePassword);
    $connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}

@session_start();

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]['timezone']);

$awardsAwardID = $_GET['awardsAwardID'];
$URL = $_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_POST['address'])."/awards_manage_edit.php&awardsAwardID=$awardsAwardID&search=".$_GET['search'];

if (isActionAccessible($guid, $connection2, '/modules/Awards/awards_manage_edit.php') == false) {
    //Fail 0
    $URL = $URL.'&return=error0';
    header("Location: {$URL}");
} else {
    //Proceed!
    //Check if school year specified
    if ($awardsAwardID == '') {
        //Fail1
        $URL = $URL.'&return=error1';
        header("Location: {$URL}");
    } else {
        try {
            $data = array('awardsAwardID' => $awardsAwardID);
            $sql = 'SELECT * FROM awardsAward WHERE awardsAwardID=:awardsAwardID';
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
            $name = $_POST['name'];
            $active = $_POST['active'];
            $category = $_POST['category'];
            $description = $_POST['description'];
            $logoLicense = $_POST['logoLicense'];
            $gibbonYearGroupIDList = '';
            for ($i = 0; $i < $_POST['count']; ++$i) {
                if (isset($_POST["gibbonYearGroupIDCheck$i"])) {
                    if ($_POST["gibbonYearGroupIDCheck$i"] == 'on') {
                        $gibbonYearGroupIDList = $gibbonYearGroupIDList.$_POST["gibbonYearGroupID$i"].',';
                    }
                }
            }
            $gibbonYearGroupIDList = substr($gibbonYearGroupIDList, 0, (strlen($gibbonYearGroupIDList) - 1));

            if ($name == '' or $active == '' or $category == '') {
                //Fail 3
                $URL = $URL.'&return=error3';
                header("Location: {$URL}");
            } else {
                //Sort out logo
                $logo = $row['logo'];
                if ($_FILES['file']['tmp_name'] != '') {
                    $time = time();
                    //Check for folder in uploads based on today's date
                    $path = $_SESSION[$guid]['absolutePath'];
                    if (is_dir($path.'/uploads/'.date('Y', $time).'/'.date('m', $time)) == false) {
                        mkdir($path.'/uploads/'.date('Y', $time).'/'.date('m', $time), 0777, true);
                    }

                    $unique = false;
                    $count = 0;
                    while ($unique == false and $count < 100) {
                        $suffix = randomPassword(16);
                        if ($count == 0) {
                            $logo = 'uploads/'.date('Y', $time).'/'.date('m', $time).'/award_'.str_replace(' ', '_', trim($name))."_$suffix".strrchr($_FILES['file']['name'], '.');
                        } else {
                            $logo = 'uploads/'.date('Y', $time).'/'.date('m', $time).'/award_'.str_replace(' ', '_', trim($name))."_$suffix"."_$count".strrchr($_FILES['file']['name'], '.');
                        }

                        if (!(file_exists($path.'/'.$logo))) {
                            $unique = true;
                        }
                        ++$count;
                    }
                    if (!(move_uploaded_file($_FILES['file']['tmp_name'], $path.'/'.$logo))) {
                        $logo = '';
                    }
                }

                //Write to database
                try {
                    $data = array('name' => $name, 'active' => $active, 'category' => $category, 'description' => $description, 'logo' => $logo, 'logoLicense' => $logoLicense, 'gibbonYearGroupIDList' => $gibbonYearGroupIDList, 'awardsAwardID' => $awardsAwardID);
                    $sql = 'UPDATE awardsAward SET name=:name, active=:active, category=:category, description=:description, logo=:logo, logoLicense=:logoLicense, gibbonYearGroupIDList=:gibbonYearGroupIDList WHERE awardsAwardID=:awardsAwardID';
                    $result = $connection2->prepare($sql);
                    $result->execute($data);
                } catch (PDOException $e) {
                    //Fail 2
                    $URL = $URL.'&return=error5';
                    header("Location: {$URL}");
                    exit();
                }

                //Success 0
                $URL = $URL.'&return=success0';
                header("Location: {$URL}");
            }
        }
    }
}
