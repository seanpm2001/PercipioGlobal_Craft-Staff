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

        $this->deleteBenefitTypes();
        $this->createTables();

        Craft::$app->db->schema->refresh();
    }

    public function deleteBenefitTypes(): void
    {
        if(Craft::$app->db->schema->getTableSchema('{{%staff_benefit_variant_dental%}}') !== null){ $this->dropTable('{{%staff_benefit_variant_dental%}}'); }
        if(Craft::$app->db->schema->getTableSchema('{{%staff_benefit_variant_gcic%}}') !== null){ $this->dropTable('{{%staff_benefit_variant_gcic%}}'); }
        if(Craft::$app->db->schema->getTableSchema('{{%staff_benefit_variant_gdis%}}') !== null){ $this->dropTable('{{%staff_benefit_variant_gdis%}}'); }
        if(Craft::$app->db->schema->getTableSchema('{{%staff_benefit_variant_gip%}}') !== null){ $this->dropTable('{{%staff_benefit_variant_gip%}}'); }
        if(Craft::$app->db->schema->getTableSchema('{{%staff_benefit_variant_gla%}}') !== null){ $this->dropTable('{{%staff_benefit_variant_gla%}}'); }
        if(Craft::$app->db->schema->getTableSchema('{{%staff_benefit_variant_hcp%}}') !== null){ $this->dropTable('{{%staff_benefit_variant_hcp%}}'); }
        if(Craft::$app->db->schema->getTableSchema('{{%staff_benefit_variant_hs%}}') !== null){ $this->dropTable('{{%staff_benefit_variant_hs%}}'); }
        if(Craft::$app->db->schema->getTableSchema('{{%staff_benefit_variant_pmi%}}') !== null){ $this->dropTable('{{%staff_benefit_variant_pmi%}}'); }
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
            ]);
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

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENEFIT_VARIANT_GCIC);
        if ($tableSchema === null) {
            $this->createTable(Table::BENEFIT_VARIANT_GCIC, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
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
            $this->addForeignKey(null, Table::BENEFIT_VARIANT_GCIC, ['policyId'], Table::BENEFIT_POLICIES, ['id'], 'CASCADE', 'CASCADE');
        }

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

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m220829_131828_group_benefits cannot be reverted.\n";
        return false;
    }
}
