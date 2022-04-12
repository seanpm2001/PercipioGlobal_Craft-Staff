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

use craft\db\ActiveRecord;
use percipiolondon\staff\db\Table;

/**
 * @property int $pensionSchemeId;
 * @property int $workerGroupId;
 * @property int $teachersPensionDetails;
 * @property string $forcedTier;
 *
 * @property string $staffologyId;
 * @property string $contributionLevelType;
 * @property string $startDate;
 * @property string $memberReferenceNumber;
 * @property boolean $overrideContributions;
 * @property double $employeeContribution;
 * @property boolean $employeeContributionIsPercentage;
 * @property double $employerContribution;
 * @property boolean $employerContributionIsPercentage;
 * @property double $employerContributionTopUpPercentage;
 * @property double $isAeQualifyingScheme;
 * @property double $isTeachersPension;
 * @property string $aeStatusAtJoining;
 * @property double $additionalVoluntaryContribution;
 * @property boolean $avcIsPercentage;
 * @property boolean $exitViaProvider;
 * @property boolean $forceEnrolment;
 * @property boolean $autoEnrolled;
 */

class Pensions extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    /**
     * Declares the name of the database table associated with this AR class.
     * By default this method returns the class name as the table name by calling [[Inflector::camel2id()]]
     * with prefix [[Connection::tablePrefix]]. For example if [[Connection::tablePrefix]] is `tbl_`,
     * `Customer` becomes `tbl_customer`, and `OrderItem` becomes `tbl_order_item`. You may override this method
     * if the table is not named after this convention.
     *
     * By convention, tables created by plugins should be prefixed with the plugin
     * name and an underscore.
     *
     * @return string the table name
     */
    public static function tableName(): string
    {
        return Table::PENSIONS;
    }
}
