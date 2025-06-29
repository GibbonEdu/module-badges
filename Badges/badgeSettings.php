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
use Gibbon\Domain\System\SettingGateway;

if (isActionAccessible($guid, $connection2, '/modules/Badges/badgeSettings.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo __('You do not have access to this action.');
    echo '</div>';
} else {
    //Proceed!
    $page->breadcrumbs->add(__('Badges Settings'));
	
	$form = Form::create('action', $session->get('absoluteURL','').'/modules/'.$session->get('module').'/badgeSettingsProcess.php');
	
	$form->addHiddenValue('address', $session->get('address'));

	$setting = $container->get(SettingGateway::class)->getSettingByScope('Badges', 'badgeCategories', true);
	$row = $form->addRow();
		$row->addLabel($setting['name'], __($setting['nameDisplay']))->description(__($setting['description']));
		$row->addTextArea($setting['name'])->setValue($setting['value'])->isRequired()->setRows(4);

	$row = $form->addRow();
		$row->addFooter();
		$row->addSubmit();
	
	echo $form->getOutput();
}
?>
