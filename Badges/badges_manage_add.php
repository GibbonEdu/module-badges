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

//Module includes
use Gibbon\Forms\Form;
use Gibbon\FileUploader;
use Gibbon\Forms\DatabaseFormFactory;

include './modules/Badges/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Badges/badges_manage_add.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    //Proceed!
    $page->breadcrumbs
            ->add(__('Manage Badges'),'badges_manage.php')
            ->add(__('Add Badges'));

    $returns = array();
    $editLink = '';
    if (isset($_GET['editID'])) {
        $editLink = $_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Badges/badges_manage_edit.php&badgesBadgeID='.$_GET['editID'].'&search='.$_GET['search'].'&category='.$_GET['category'];
    }
    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], $editLink, null);
    }

    if ($_GET['search'] != '' || $_GET['category'] != '') {
        echo "<div class='linkTop'>";
        echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Badges/badges_manage.php&search='.$_GET['search'].'&category='.$_GET['category']."'>Back to Search Results</a>";
        echo '</div>';
    }

    ?>
<?php

	$form = Form::create('action', $_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module'].'/badges_manage_addProcess.php');
	
		$form->setFactory(DatabaseFormFactory::create($pdo));
	
		$form->addHiddenValue('address', $_SESSION[$guid]['address']);

		$row = $form->addRow();
			$row->addLabel('name', __('Name'));
	    	$row->addTextField('name')->required()->maxLength(10);

		$row = $form->addRow();
			$row->addLabel('active', __('Active'));
			$row->addYesNo('active')->required();
		
		$categories = getSettingByScope($connection2, 'Badges', 'badgeCategories');
			$categories = ($categories != '' ? explode(',', $categories) : '');
			$row = $form->addRow();
			$row->addLabel('category', __('Category'));
			$row->addSelect('category')->fromArray($categories)->selected($category)->required()->placeholder();		

		$row = $form->addRow();
			$row->addLabel('description', __('Description'));
			$row->addTextArea('description')->setRows(8);
		
		$fileUploader = new FileUploader($pdo, $gibbon->session);
		
		$row = $form->addRow();
                $row->addLabel('logo', __('Logo'));
                $file = $row->addFileUpload('logo')->accepts($fileUploader->getFileExtensions('Graphics/Design'));
		
		$row = $form->addRow();
			$row->addLabel('logoLicense', __('Logo License/Credits'));
			$row->addTextArea('logoLicense')->setRows(8);
			
		$row = $form->addRow();
			$row->addSubmit();

		echo $form->getOutput();

?>    
	<?php

}
?>
