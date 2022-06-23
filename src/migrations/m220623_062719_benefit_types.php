<?php

namespace percipiolondon\staff\migrations;

use Craft;
use craft\db\Migration;
use craft\db\Table as CraftTable;
use percipiolondon\staff\db\Table;

/**
 * m220623_062719_benefit_types migration.
 */
class m220623_062719_benefit_types extends Migration
{
    /**
     * @var string
     */
    public string|null $driver;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;

        if ($this->createTables()) {
            $this->createIndexes();
            $this->createForeignKeys();
            Craft::$app->db->schema->refresh();
        }
    }

    /**
     * Creates the tables for Staff Management
     */
    public function createTables(): bool {
        $tableCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENETFIT_TYPE_DENTAL);
        if ($tableSchema === null) {
            $this->createTable(Table::BENETFIT_TYPE_DENTAL, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'siteId' => $this->integer(),
                //FK
                //intern
                'providerId' => $this->integer(),
                //generic fields
                'internalCode' => $this->string(255)->notNull(),
                'status' => $this->string(255)->notNull(),
                'policyName' => $this->string(255)->notNull(),
                'policyNumber' => $this->string(255)->notNull(),
                'policyHolder' => $this->string(255)->notNull(),
                'content' => $this->longText(),
                'policyStartDate' => $this->dateTime()->notNull(),
                'policyRenewalDate' => $this->dateTime()->notNull(),
                'paymentFrequency' => $this->enum('status', ['annual', 'monthly'])->notNull(),
                'commissionRate' => $this->float()->notNull(),
            ]);

            $tableCreated = true;
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENETFIT_TYPE_GROUP_CRITICAL_ILLNESS_COVER);
        if ($tableSchema === null) {
            $this->createTable(Table::BENETFIT_TYPE_GROUP_CRITICAL_ILLNESS_COVER, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'siteId' => $this->integer(),
                //FK
                //intern
                'providerId' => $this->integer(),
                //generic fields
                'internalCode' => $this->string(255)->notNull(),
                'status' => $this->string(255)->notNull(),
                'policyName' => $this->string(255)->notNull(),
                'policyNumber' => $this->string(255)->notNull(),
                'policyHolder' => $this->string(255)->notNull(),
                'content' => $this->longText(),
                'policyStartDate' => $this->dateTime()->notNull(),
                'policyRenewalDate' => $this->dateTime()->notNull(),
                'paymentFrequency' => $this->enum('status', ['annual', 'monthly'])->notNull(),
                'commissionRate' => $this->float()->notNull(),
                //custom fields
                'rateReviewGuaranteeDate' => $this->dateTime(),
                'costingBasis' => $this->enum('costingBasis', ['unit', 'sp']),
                'unitRate' => $this->float(),
                'unitRateSuffix' => $this->enum('unitRateSuffix', ['%', '‰']),
                'freeCoverLevelAutomaticAcceptanceLimit' => $this->float(),
                'dateRefreshFrequency' => $this->enum('dateRefreshFrequency', ['annual', 'monthly'])
            ]);

            $tableCreated = true;
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENETFIT_TYPE_GROUP_DEATH_IN_SERVICE);
        if ($tableSchema === null) {
            $this->createTable(Table::BENETFIT_TYPE_GROUP_DEATH_IN_SERVICE, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'siteId' => $this->integer(),
                //FK
                //intern
                'providerId' => $this->integer(),
                //generic fields
                'internalCode' => $this->string(255)->notNull(),
                'status' => $this->string(255)->notNull(),
                'policyName' => $this->string(255)->notNull(),
                'policyNumber' => $this->string(255)->notNull(),
                'policyHolder' => $this->string(255)->notNull(),
                'content' => $this->longText(),
                'policyStartDate' => $this->dateTime()->notNull(),
                'policyRenewalDate' => $this->dateTime()->notNull(),
                'paymentFrequency' => $this->enum('status', ['annual', 'monthly'])->notNull(),
                'commissionRate' => $this->float()->notNull(),
                //custom fields
                'rateReviewGuaranteeDate' => $this->dateTime(),
                'costingBasis' => $this->enum('costingBasis', ['unit', 'sp']),
                'unitRate' => $this->float(),
                'unitRateSuffix' => $this->enum('unitRateSuffix', ['%', '‰']),
                'freeCoverLevelAutomaticAcceptanceLimit' => $this->float(),
                'dateRefreshFrequency' => $this->enum('dateRefreshFrequency', ['annual', 'monthly']),
                'pensionSchemeTaxReferenceNumber' => $this->string(255),
                'dateOfTrustDeed' => $this->float(),
                'eventLimit' => $this->float()
            ]);

            $tableCreated = true;
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENETFIT_TYPE_GROUP_INCOME_PROTECTION);
        if ($tableSchema === null) {
            $this->createTable(Table::BENETFIT_TYPE_GROUP_INCOME_PROTECTION, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'siteId' => $this->integer(),
                //FK
                //intern
                'providerId' => $this->integer(),
                //generic fields
                'internalCode' => $this->string(255)->notNull(),
                'status' => $this->string(255)->notNull(),
                'policyName' => $this->string(255)->notNull(),
                'policyNumber' => $this->string(255)->notNull(),
                'policyHolder' => $this->string(255)->notNull(),
                'content' => $this->longText(),
                'policyStartDate' => $this->dateTime()->notNull(),
                'policyRenewalDate' => $this->dateTime()->notNull(),
                'paymentFrequency' => $this->enum('status', ['annual', 'monthly'])->notNull(),
                'commissionRate' => $this->float()->notNull(),
                //custom fields
                'rateReviewGuaranteeDate' => $this->dateTime(),
                'costingBasis' => $this->enum('costingBasis', ['unit', 'sp']),
                'unitRate' => $this->float(),
                'unitRateSuffix' => $this->enum('unitRateSuffix', ['%', '‰']),
                'freeCoverLevelAutomaticAcceptanceLimit' => $this->float(),
                'dateRefreshFrequency' => $this->enum('dateRefreshFrequency', ['annual', 'monthly'])
            ]);

            $tableCreated = true;
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENETFIT_TYPE_GROUP_LIFE_ASSURANCE);
        if ($tableSchema === null) {
            $this->createTable(Table::BENETFIT_TYPE_GROUP_LIFE_ASSURANCE, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'siteId' => $this->integer(),
                //FK
                //intern
                'providerId' => $this->integer(),
                //generic fields
                'internalCode' => $this->string(255)->notNull(),
                'status' => $this->string(255)->notNull(),
                'policyName' => $this->string(255)->notNull(),
                'policyNumber' => $this->string(255)->notNull(),
                'policyHolder' => $this->string(255)->notNull(),
                'content' => $this->longText(),
                'policyStartDate' => $this->dateTime()->notNull(),
                'policyRenewalDate' => $this->dateTime()->notNull(),
                'paymentFrequency' => $this->enum('status', ['annual', 'monthly'])->notNull(),
                'commissionRate' => $this->float()->notNull(),
                //custom fields
                'rateReviewGuaranteeDate' => $this->dateTime(),
                'costingBasis' => $this->enum('costingBasis', ['unit', 'sp']),
                'unitRate' => $this->float(),
                'unitRateSuffix' => $this->enum('unitRateSuffix', ['%', '‰']),
                'freeCoverLevelAutomaticAcceptanceLimit' => $this->float(),
                'dateRefreshFrequency' => $this->enum('dateRefreshFrequency', ['annual', 'monthly']),
                'pensionSchemeTaxReferenceNumber' => $this->string(255),
                'dateOfTrustDeed' => $this->float(),
                'eventLimit' => $this->float()
            ]);

            $tableCreated = true;
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENETFIT_TYPE_HEALTH_CASH_PLAN);
        if ($tableSchema === null) {
            $this->createTable(Table::BENETFIT_TYPE_HEALTH_CASH_PLAN, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'siteId' => $this->integer(),
                //FK
                //intern
                'providerId' => $this->integer(),
                //generic fields
                'internalCode' => $this->string(255)->notNull(),
                'status' => $this->string(255)->notNull(),
                'policyName' => $this->string(255)->notNull(),
                'policyNumber' => $this->string(255)->notNull(),
                'policyHolder' => $this->string(255)->notNull(),
                'content' => $this->longText(),
                'policyStartDate' => $this->dateTime()->notNull(),
                'policyRenewalDate' => $this->dateTime()->notNull(),
                'paymentFrequency' => $this->enum('status', ['annual', 'monthly'])->notNull(),
                'commissionRate' => $this->float()->notNull(),
            ]);

            $tableCreated = true;
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENETFIT_TYPE_PRIVATE_MEDICAL_INSURANCE);
        if ($tableSchema === null) {
            $this->createTable(Table::BENETFIT_TYPE_PRIVATE_MEDICAL_INSURANCE, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'siteId' => $this->integer(),
                //FK
                //intern
                'providerId' => $this->integer(),
                //generic fields
                'internalCode' => $this->string(255)->notNull(),
                'status' => $this->string(255)->notNull(),
                'policyName' => $this->string(255)->notNull(),
                'policyNumber' => $this->string(255)->notNull(),
                'policyHolder' => $this->string(255)->notNull(),
                'content' => $this->longText(),
                'policyStartDate' => $this->dateTime()->notNull(),
                'policyRenewalDate' => $this->dateTime()->notNull(),
                'paymentFrequency' => $this->enum('status', ['annual', 'monthly'])->notNull(),
                'commissionRate' => $this->float()->notNull(),
                //custom fields
                'underwritingBasis' => $this->enum('underwritingBasis', ['moratorium', 'medical-history-disregarded', 'full-medical-underwriting']),
                'hospitalList' => $this->string(),
            ]);

            $tableCreated = true;
        }

        return $tableCreated;
    }

    /**
     * Creates the indexes
     */
    public function createIndexes(): void
    {
        $this->createIndex(null, Table::BENETFIT_TYPE_DENTAL, 'internalCode', true);
        $this->createIndex(null, Table::BENETFIT_TYPE_GROUP_CRITICAL_ILLNESS_COVER, 'internalCode', true);
        $this->createIndex(null, Table::BENETFIT_TYPE_GROUP_DEATH_IN_SERVICE, 'internalCode', true);
        $this->createIndex(null, Table::BENETFIT_TYPE_GROUP_INCOME_PROTECTION, 'internalCode', true);
        $this->createIndex(null, Table::BENETFIT_TYPE_GROUP_LIFE_ASSURANCE, 'internalCode', true);
        $this->createIndex(null, Table::BENETFIT_TYPE_HEALTH_CASH_PLAN, 'internalCode', true);
        $this->createIndex(null, Table::BENETFIT_TYPE_PRIVATE_MEDICAL_INSURANCE, 'internalCode', true);
    }

    public function createForeignKeys(): void
    {
        $this->addForeignKey(null, Table::BENETFIT_TYPE_DENTAL, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::BENETFIT_TYPE_GROUP_CRITICAL_ILLNESS_COVER, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::BENETFIT_TYPE_GROUP_DEATH_IN_SERVICE, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::BENETFIT_TYPE_GROUP_INCOME_PROTECTION, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::BENETFIT_TYPE_GROUP_LIFE_ASSURANCE, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::BENETFIT_TYPE_HEALTH_CASH_PLAN, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
        $this->addForeignKey(null, Table::BENETFIT_TYPE_PRIVATE_MEDICAL_INSURANCE, ['id'], CraftTable::ELEMENTS, ['id'], 'CASCADE', 'CASCADE' );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m220623_062719_benefit_types cannot be reverted.\n";
        return false;
    }
}
