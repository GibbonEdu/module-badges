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
use Gibbon\Forms\Form;
use Gibbon\FileUploader;
use Gibbon\Http\Url;
use Gibbon\Domain\System\SettingGateway;

include './modules/Badges/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Badges/badges_manage_edit.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    //Proceed!
    $page->breadcrumbs
        ->add(__('Manage Badges'),'badges_manage.php')
        ->add(__('Edit Badges'));

    $search = $_GET['search'] ?? '';
    $category = $_GET['category'] ?? '';
    $badgesBadgeID = $_GET['badgesBadgeID'] ?? null;
    
    if (empty($badgesBadgeID)) {
        echo "<div class='error'>";
        echo 'You have not specified a policy.';
        echo '</div>';
    } else {
        try {
            $data = array('badgesBadgeID' => $badgesBadgeID);
            $sql = 'SELECT * FROM badgesBadge WHERE badgesBadgeID=:badgesBadgeID';
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            echo "<div class='error'>".$e->getMessage().'</div>';
        }

        if ($result->rowCount() != 1) {
            echo "<div class='error'>";
            echo 'The selected policy does not exist.';
            echo '</div>';
        } else {
            //Let's go!
            $values = $result->fetch();

            if (!empty($search) || !empty($category)) {
                $params = [
                    "search" => $search,
                    "category" => $category
                ];
                $page->navigator->addSearchResultsAction(Url::fromModuleRoute('Badges', 'badges_manage.php')->withQueryParams($params));
            }

            $form = Form::create('badges', $session->get('absoluteURL','').'/modules/'.$session->get('module')."/badges_manage_editProcess.php?badgesBadgeID=$badgesBadgeID&search=$search&category=$category");

            $form->addHiddenValue('address', $session->get('address'));

            $row = $form->addRow();
                $row->addLabel('name', __('Name'));
                $row->addTextField('name')->required()->maxLength(50);

            $row = $form->addRow();
                $row->addLabel('license', __m('License'))->description(__m('Does granting this license the recipient to do something?'));
                $row->addYesNo('license')->required()->setValue($values['license']);

            $row = $form->addRow();
                $row->addLabel('active', __('Active'));
                $row->addYesNo('active')->required();

            $categories = $container->get(SettingGateway::class)->getSettingByScope('Badges', 'badgeCategories');
            $categories = !empty($categories) ? array_map('trim', explode(',', $categories)) : [];
            $row = $form->addRow();
                $row->addLabel('category', __('Category'));
                $row->addSelect('category')->fromArray($categories)->required()->placeholder();

            $row = $form->addRow();
                $row->addLabel('description', __('Description'));
                $row->addTextArea('description');

            $fileUploader = new FileUploader($pdo, $session);

            $row = $form->addRow();
                $row->addLabel('file', __('Logo'))->description(__('240px x 240px'));
                $row->addFileUpload('file')
                    ->accepts($fileUploader->getFileExtensions('Graphics/Design'))
                    ->setAttachment('logo', $session->get('absoluteURL',''), $values['logo']);

            $row = $form->addRow();
                $row->addLabel('logoLicense', __('Logo License/Credits'));
                $row->addTextArea('logoLicense');

            $row = $form->addRow();
                $row->addSubmit();

            $form->loadAllValuesFrom($values);

            echo $form->getOutput();
        }
    }
}
