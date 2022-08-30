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
        if(Craft::$app->db->schema->getTableSchema('{{%staff_benefit_type_dental%}}') !== null){ $this->dropTable('{{%staff_benefit_type_dental%}}'); }
        if(Craft::$app->db->schema->getTableSchema('{{%staff_benefit_type_group_critical_illness_cover%}}') !== null){ $this->dropTable('{{%staff_benefit_type_group_critical_illness_cover%}}'); }
        if(Craft::$app->db->schema->getTableSchema('{{%staff_benefit_type_group_death_in_service%}}') !== null){ $this->dropTable('{{%staff_benefit_type_group_death_in_service%}}'); }
        if(Craft::$app->db->schema->getTableSchema('{{%staff_benefit_type_group_income_protection%}}') !== null){ $this->dropTable('{{%staff_benefit_type_group_income_protection%}}'); }
        if(Craft::$app->db->schema->getTableSchema('{{%staff_benefit_type_group_life_assurance%}}') !== null){ $this->dropTable('{{%staff_benefit_type_group_life_assurance%}}'); }
        if(Craft::$app->db->schema->getTableSchema('{{%staff_benefit_type_health_cash_plan%}}') !== null){ $this->dropTable('{{%staff_benefit_type_health_cash_plan%}}'); }
        if(Craft::$app->db->schema->getTableSchema('{{%staff_benefit_type_private_medical_insurance%}}') !== null){ $this->dropTable('{{%staff_benefit_type_private_medical_insurance%}}'); }
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

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENEFIT_TRS);
        if ($tableSchema === null) {
            $this->createTable(Table::BENEFIT_TRS, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //generic fields
                'title' => $this->string(255)->notNull(),
                'monetaryValue' => $this->float(),
                'startDate' => $this->dateTime()->notNull(),
                'endDate' => $this->dateTime()->notNull()
            ]);
        }

        $tableSchema = Craft::$app->db->schema->getTableSchema(Table::BENEFIT_VARIANT_DENTAL);
        if ($tableSchema === null) {
            $this->createTable(Table::BENEFIT_VARIANT_DENTAL, [
                'id' => $this->primaryKey(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                //FK
                //intern
                'trsId' => $this->integer(),
                'policyId' => $this->integer(),
                //generic fields
                'name' => $this->string(255)->notNull()
            ]);

            // foreign key
            $this->addForeignKey(null, Table::BENEFIT_VARIANT_DENTAL, ['trsId'], Table::BENEFIT_TRS, ['id'], 'CASCADE', 'CASCADE');
            $this->addForeignKey(null, Table::BENEFIT_VARIANT_DENTAL, ['policyId'], Table::BENEFIT_POLICIES, ['id'], 'CASCADE', 'CASCADE');
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
                'dentalId' => $this->integer()
            ]);

            // foreign key
            $this->addForeignKey(null, Table::BENEFIT_EMPLOYEES_VARIANTS, ['employeeId'], Table::EMPLOYEES, ['id'], 'CASCADE', 'CASCADE');
            $this->addForeignKey(null, Table::BENEFIT_EMPLOYEES_VARIANTS, ['dentalId'], Table::BENEFIT_VARIANT_DENTAL, ['id'], 'CASCADE', 'CASCADE');
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
