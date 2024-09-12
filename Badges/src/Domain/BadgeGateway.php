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

namespace Gibbon\Module\Badges\Domain;

use Gibbon\Domain\Traits\TableAware;
use Gibbon\Domain\QueryCriteria;
use Gibbon\Domain\QueryableGateway;

class BadgeGateway extends QueryableGateway
{
    use TableAware;

    private static $tableName = 'badgesBadge';
    private static $primaryKey = 'badgesBadgeID';

    public function queryBadges($criteria, $search = null, $category = null)
    {
        $query = $this
            ->newQuery()
            ->cols(['*'])
            ->from($this->getTableName());

        if (!empty($search)) {
            $query->where("name LIKE CONCAT('%', :search, '%')")
                ->bindValue('search', $search);
        }

        if (!empty($category)) {
            $query->where("category=:category")
                ->bindValue('category', $category);
        }

        return $this->runQuery($query, $criteria);
    }

    public function queryBadgeGrants($criteria, $gibbonSchoolYearID, $gibbonPersonID = null, $badgesBadgeID = null)
    {
        $query = $this
            ->newQuery()
            ->cols(['badgesBadgeStudent.*', 'badgesBadge.name', 'badgesBadge.logo', 'badgesBadge.description', 'surname', 'preferredName'])
            ->from('badgesBadgeStudent')
            ->innerJoin('badgesBadge', 'badgesBadgeStudent.badgesBadgeID=badgesBadge.badgesBadgeID')
            ->innerJoin('gibbonPerson', 'badgesBadgeStudent.gibbonPersonID=gibbonPerson.gibbonPersonID')
            ->where("badgesBadgeStudent.gibbonSchoolYearID=:gibbonSchoolYearID")
            ->bindValue('gibbonSchoolYearID', $gibbonSchoolYearID);

        if (!empty($gibbonPersonID)) {
            $query->where("badgesBadgeStudent.gibbonPersonID=:gibbonPersonID")
                ->bindValue('gibbonPersonID', $gibbonPersonID);
        }

        if (!empty($badgesBadgeID)) {
            $query->where("badgesBadgeStudent.badgesBadgeID=:badgesBadgeID")
                ->bindValue('badgesBadgeID', $badgesBadgeID);
        }

        return $this->runQuery($query, $criteria);
    }

}
