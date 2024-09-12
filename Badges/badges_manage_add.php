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

use Gibbon\Http\Url;
use Gibbon\Forms\Form;
use Gibbon\FileUploader;
use Gibbon\Domain\System\SettingGateway;

include './modules/Badges/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Badges/badges_manage_add.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    //Proceed!
    $page->breadcrumbs
            ->add(__('Manage Badges'), 'badges_manage.php')
            ->add(__('Add Badges'));

    $editID = $_GET['editID'] ?? '';
    $search = $_GET['search'] ?? '';
    $category = $_GET['category'] ?? '';

    $editLink = !empty($editID) ? $gibbon->session->get('absoluteURL','')."/index.php?q=/modules/Badges/badges_manage_edit.php&badgesBadgeID=$editID&search=$search&category=$category" : '';
    $page->return->setEditLink($editLink);

    if (!empty($search) || !empty($category)) {
        $params = [
            "search" => $search,
            "category" => $category
        ];
        $page->navigator->addSearchResultsAction(Url::fromModuleRoute('Badges', 'badges_manage.php')->withQueryParams($params));
    }

    $form = Form::create('badges', $gibbon->session->get('absoluteURL','').'/modules/'.$gibbon->session->get('module')."/badges_manage_addProcess.php?search=$search&category=$category");

    $form->addHiddenValue('address', $gibbon->session->get('address'));

    $row = $form->addRow();
        $row->addLabel('name', __('Name'));
        $row->addTextField('name')->required()->maxLength(50);

    $row = $form->addRow();
        $row->addLabel('license', __m('License'))->description(__m('Does granting this license the recipient to do something?'));
        $row->addYesNo('license')->required()->selected('N');

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

    $fileUploader = new FileUploader($pdo, $gibbon->session);

    $row = $form->addRow();
        $row->addLabel('file', __('Logo'))->description(__('240px x 240px'));
        $row->addFileUpload('file')->accepts($fileUploader->getFileExtensions('Graphics/Design'));

    $row = $form->addRow();
        $row->addLabel('logoLicense', __('Logo License/Credits'));
        $row->addTextArea('logoLicense');

    $row = $form->addRow();
        $row->addSubmit();

    echo $form->getOutput();
}
