<?php

namespace percipiolondon\staff\migrations;

use Craft;
use craft\db\Migration;
use percipiolondon\staff\db\Table;

/**
 * m220829_131828_group_benefits migration.
 */
class m220829_131828_group_benefits extends Migration
{
    public $driver;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;

        $this->createTables();

        Craft::$app->db->schema->refresh();
    }

    public function createTables(): bool
    {
        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENEFIT_PROVIDERS);
        if ($tableSchema === null) {
            $this->createTable(Table::BENEFIT_PROVIDERS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                //fields
                'name' => $this->string(255)->notNull(),
                'logo' => $this->integer(),
                'url' => $this->string(255)->notNull(),
                'content' => $this->longText()
            ]);

            $tableCreated = true;
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENEFIT_TYPES);
        if ($tableSchema === null) {
            $this->createTable(Table::BENEFIT_TYPES, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                //generic fields
                'name' => $this->string(255)->notNull(),
                'slug' => $this->string(255)->notNull(),
            ]);
        } else {
            $this->addColumn(Table::BENEFIT_TYPES, 'slug', $this->string(255)->notNull());
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENEFIT_POLICIES);
        if ($tableSchema === null) {
            $this->createTable(Table::BENEFIT_POLICIES, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'providerId' => $this->integer(),
                'employerId' => $this->integer(),
                'benefitTypeId' => $this->integer(),
                //generic fields
                'internalCode' => $this->string(255)->notNull(),
                'status' => $this->string(255)->notNull(),
                'policyName' => $this->string(255)->notNull(),
                'policyNumber' => $this->string(255)->notNull(),
                'policyHolder' => $this->string(255)->notNull(),
                'policyStartDate' => $this->dateTime()->notNull(),
                'policyRenewalDate' => $this->dateTime()->notNull(),
                'paymentFrequency' => $this->enum('paymentFrequency', ['annual', 'monthly'])->notNull(),
                'commissionRate' => $this->float()->notNull(),
                'description' => $this->longText(),
            ]);

            // index
            $this->createIndex(null, Table::BENEFIT_POLICIES, 'internalCode', true);

            // foreign key
            $this->addForeignKey(null, Table::BENEFIT_POLICIES, ['providerId'], Table::BENEFIT_PROVIDERS, ['id'], 'CASCADE', 'CASCADE');
            $this->addForeignKey(null, Table::BENEFIT_POLICIES, ['employerId'], Table::EMPLOYERS, ['id'], 'CASCADE', 'CASCADE');
            $this->addForeignKey(null, Table::BENEFIT_POLICIES, ['benefitTypeId'], Table::BENEFIT_TYPES, ['id'], 'CASCADE', 'CASCADE');
        }

        $this->_createVariants();

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENEFIT_TRS);
        if ($tableSchema === null) {
            $this->createTable(Table::BENEFIT_TRS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                'variantId' => $this->integer(),
                //generic fields
                'title' => $this->string(255)->notNull(),
                'monetaryValue' => $this->float(),
                'startDate' => $this->dateTime()->notNull(),
                'endDate' => $this->dateTime()->notNull()
            ]);

            // foreign key
            $this->addForeignKey(null, Table::BENEFIT_TRS, ['variantId'], Table::BENEFIT_VARIANT, ['id'], 'CASCADE', 'CASCADE');
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENEFIT_EMPLOYEES_VARIANTS);
        if ($tableSchema === null) {
            $this->createTable(Table::BENEFIT_EMPLOYEES_VARIANTS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'employeeId' => $this->integer(),
                'variantId' => $this->integer()
            ]);

            // foreign key
            $this->addForeignKey(null, Table::BENEFIT_EMPLOYEES_VARIANTS, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
            $this->addForeignKey(null, Table::BENEFIT_EMPLOYEES_VARIANTS, ['variantId'], \craft\db\Table::ELEMENTS, ['id'], 'CASCADE', 'CASCADE');
        }

        return true;
    }

    private function _createVariants(): void
    {
        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENEFIT_VARIANT_GCIC);
        if ($tableSchema === null) {
            $this->createTable(Table::BENEFIT_VARIANT_GCIC, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //custom fields
                'rateReviewGuaranteeDate' => $this->dateTime(),
                'costingBasis' => $this->enum('costingBasis', ['unit', 'sp']),
                'unitRate' => $this->float(),
                'unitRateSuffix' => $this->enum('unitRateSuffix', ['%', '‰']),
                'freeCoverLevelAutomaticAcceptanceLimit' => $this->float(),
                'dateRefreshFrequency' => $this->enum('dateRefreshFrequency', ['annual', 'monthly'])
            ]);

            // foreign key
            $this->addForeignKey(null, Table::BENEFIT_VARIANT_GCIC, ['id'], \craft\db\Table::ELEMENTS, ['id'], 'CASCADE', 'CASCADE');
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENEFIT_VARIANT_GDIS);
        if ($tableSchema === null) {
            $this->createTable(Table::BENEFIT_VARIANT_GDIS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //custom fields
                'rateReviewGuaranteeDate' => $this->dateTime(),
                'costingBasis' => $this->enum('costingBasis', ['unit', 'sp']),
                'unitRate' => $this->float(),
                'unitRateSuffix' => $this->enum('unitRateSuffix', ['%', '‰']),
                'freeCoverLevelAutomaticAcceptanceLimit' => $this->float(),
                'dateRefreshFrequency' => $this->enum('dateRefreshFrequency', ['annual', 'monthly']),
                'pensionSchemeTaxReferenceNumber' => $this->string(255),
                'dateOfTrustDeed' => $this->dateTime(),
                'eventLimit' => $this->float()
            ]);

            // foreign key
            $this->addForeignKey(null, Table::BENEFIT_VARIANT_GDIS, ['id'], \craft\db\Table::ELEMENTS, ['id'], 'CASCADE', 'CASCADE');
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENEFIT_VARIANT_GIP);
        if ($tableSchema === null) {
            $this->createTable(Table::BENEFIT_VARIANT_GIP, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //custom fields
                'rateReviewGuaranteeDate' => $this->dateTime(),
                'costingBasis' => $this->enum('costingBasis', ['unit', 'sp']),
                'unitRate' => $this->float(),
                'unitRateSuffix' => $this->enum('unitRateSuffix', ['%', '‰']),
                'freeCoverLevelAutomaticAcceptanceLimit' => $this->float(),
                'dateRefreshFrequency' => $this->enum('dateRefreshFrequency', ['annual', 'monthly'])
            ]);

            // foreign key
            $this->addForeignKey(null, Table::BENEFIT_VARIANT_GIP, ['id'], \craft\db\Table::ELEMENTS, ['id'], 'CASCADE', 'CASCADE');
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENEFIT_VARIANT_GLA);
        if ($tableSchema === null) {
            $this->createTable(Table::BENEFIT_VARIANT_GLA, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
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

            // foreign key
            $this->addForeignKey(null, Table::BENEFIT_VARIANT_GLA, ['id'], \craft\db\Table::ELEMENTS, ['id'], 'CASCADE', 'CASCADE');
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENEFIT_VARIANT_PMI);
        if ($tableSchema === null) {
            $this->createTable(Table::BENEFIT_VARIANT_PMI, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //custom fields
                'underwritingBasis' => $this->enum('underwritingBasis', ['moratorium', 'medical-history-disregarded', 'full-medical-underwriting']),
                'hospitalList' => $this->string(),
            ]);

            // foreign key
            $this->addForeignKey(null, Table::BENEFIT_VARIANT_PMI, ['id'], \craft\db\Table::ELEMENTS, ['id'], 'CASCADE', 'CASCADE');
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENEFIT_VARIANT);
        if ($tableSchema === null) {
            $this->createTable(Table::BENEFIT_VARIANT, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'policyId' => $this->integer(),
                //generic fields
                'name' => $this->string(255)->notNull()
            ]);

            // foreign key
            $this->addForeignKey(null, Table::BENEFIT_VARIANT, ['id'], \craft\db\Table::ELEMENTS, ['id'], 'CASCADE', 'CASCADE');
            $this->addForeignKey(null, Table::BENEFIT_VARIANT, ['policyId'], Table::BENEFIT_POLICIES, ['id'], 'CASCADE', 'CASCADE');
        }

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m220829_131828_group_benefits cannot be reverted.\n";
        return false;
    }
}
