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

use Gibbon\Forms\Form;
use Gibbon\Services\Format;
use Gibbon\Tables\DataTable;
use Gibbon\Domain\System\SettingGateway;
use Gibbon\Module\Badges\Domain\BadgeGateway;

include './modules/Badges/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Badges/badges_manage.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    //Proceed!
    $page->breadcrumbs->add(__('Manage Badges'));

    // FILTER
    $search = $_GET['search'] ?? '';
    $category = $_GET['category'] ?? '';

    $form = Form::create('search', $gibbon->session->get('absoluteURL','').'/index.php', 'get');
    $form->setTitle(__('Search & Filter'));
    $form->addClass('noIntBorder');

    $form->addHiddenValue('q', '/modules/'.$gibbon->session->get('module').'/badges_manage.php');
    $form->addHiddenValue('address', '/modules/' . $gibbon->session->get('address'));

    $row = $form->addRow();
        $row->addLabel('search', __('Search For'))->description(__('Name'));
        $row->addTextField('search')->setValue($search);

    $categories = $container->get(SettingGateway::class)->getSettingByScope('Badges', 'badgeCategories');
    $categories = !empty($categories) ? array_map('trim', explode(',', $categories)) : [];
    $row = $form->addRow();
        $row->addLabel('category', __('Category'));
        $row->addSelect('category')->fromArray($categories)->selected($category)->placeholder();

    $row = $form->addRow();
        $row->addSearchSubmit($gibbon->session, __('Clear Search'));

    echo $form->getOutput();

    $badgeGateway = $container->get(BadgeGateway::class);

    // QUERY
    $criteria = $badgeGateway->newQueryCriteria(true)
        ->sortBy(['name'])
        ->pageSize(50)
        ->fromPOST();

    $badges = $badgeGateway->queryBadges($criteria, $search, $category);


    // TABLE
    $table = DataTable::createPaginated('badges', $criteria);
    $table->setTitle(__('View'));

    $table->modifyRows(function ($unit, $row) {
        if ($unit['active'] != 'Y') $row->addClass('error');
        return $row;
    });

    $table->addHeaderAction('add', __('Add'))
        ->addParam('search', $search)
        ->addParam('category', $category)
        ->setURL('/modules/Badges/badges_manage_add.php')
        ->displayLabel();

    $table->addExpandableColumn('description');

    $table->addColumn('logo', __('Logo'))
        ->format(function ($values) use ($session) {
            if ($values['logo'] != '') {
                return "<img class='user' style='max-width: 150px' src='" . $session->get('absoluteURL','') . '/' . $values['logo'] . "'/>";
            } else {
                return "<img class='user' style='max-width: 150px' src='" . $session->get('absoluteURL','') . '/themes/' . $session->get('gibbonThemeName') . "/img/anonymous_240_square.jpg'/>";
            }
        });

    $table->addColumn('name', __('Name'));

    $table->addColumn('license', __('License'))
        ->format(function ($values) use ($session) {
            if ($values['license'] == "Y") {
                return "<img title='" . __('Yes'). "' src='./themes/Default/img/iconTick.png' />";
            }
        });

    $table->addColumn('category', __('Category'));

    $table->addColumn('active', __('active'))
        ->format(function ($values) {
            return Format::yesNo(__($values['active']));
        });

    $actions = $table->addActionColumn()
        ->addParam('badgesBadgeID')
        ->addParam('search', $search)
        ->addParam('category', $category)
        ->format(function ($resource, $actions) {
            $actions->addAction('edit', __('Edit'))
                ->setURL('/modules/Badges/badges_manage_edit.php');
            $actions->addAction('delete', __('Delete'))
                ->setURL('/modules/Badges/badges_manage_delete.php');
        });

    echo $table->render($badges);
}
?>
