<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\staff\records;

use percipiolondon\staff\Staff;

use Craft;
use craft\db\ActiveRecord;
use percipiolondon\staff\db\Table;

/**
* @property string $slug;
*
* @property int $addressId;
* @property int $defaultPayOptionsId;
*
* @property string $staffologyId;
* @property string $name;
* @property string $logoUrl;
* @property string $crn;
* @property string $startYear;
* @property string $currentYear;
* @property int $employeeCount;
 */

class Employer extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    public static function tableName()
    {
        return Table::EMPLOYERS;
    }
}
