<?php

/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\staff\services;

use Craft;
use craft\base\Component;
use percipiolondon\staff\db\Table;
use percipiolondon\staff\elements\Employer;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\jobs\FetchEmployersJob;
use percipiolondon\staff\records\Address;
use percipiolondon\staff\records\Countries;
use percipiolondon\staff\records\HmrcDetails;
use percipiolondon\staff\Staff;
use yii\db\Exception;
use yii\db\Query;

/**
 * Employers Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Percipio
 * @package   Staff
 * @since     1.0.0-alpha.1
 */
class Employers extends Component
{
    // Public Methods
    // =========================================================================


    /* GETTERS */
    /**
     * @param int $employerId
     * @return array
     * @throws Exception
     */
    public function getEmployerById(int $employerId, bool $raw = false): array
    {
        $query = new Query();
        $query->from(Table::EMPLOYERS)
            ->where('id = ' . $employerId)
            ->one();
        $command = $query->createCommand();
        $employer = $command->queryOne();

        if (!$employer) {
            return [];
        }

        if (!$raw) {
            $employer = $this->parseEmployer($employer);
        }

        if ($employer['defaultPayOptionsId'] ?? null) {
            $query = new Query();
            $query->from(Table::PAY_OPTIONS)
                ->where('id = ' . $employer['defaultPayOptionsId'])
                ->one();
            $command = $query->createCommand();
            $payOptions = $command->queryOne();

            if ($payOptions && !$raw) {
                $employer['defaultPayOptions'] = Staff::$plugin->payRuns->parsePayOptions($payOptions);
            }
        }

        return $employer;
    }

    /**
     * @param int $employerId
     * @return string
     */
    public function getEmployerNameById(int $employerId): string
    {
        $employer = Employer::findOne($employerId);

        if ($employer) {
            return $employer['name'];
        }

        return '';
    }

    /**
     * @param int $employerId
     * @return array
     */
    public function getHmrcDetailsByEmployer(int $employerId): array
    {
        $hmrcDetails = HmrcDetails::findOne(['employerId' => $employerId]);

        if (!$hmrcDetails) {
            return [];
        }

        return $hmrcDetails->toArray();
    }

    /**
     * @param int $employerId
     * @return array
     */
    public function getAddressByEmployer(int $employerId): array
    {
        $address = Address::findOne(['employerId' => $employerId]);

        if (!$address) {
            return [];
        }

        $address = $address->toArray();

        //country
        $country = Countries::findOne($address['countryId']);
        if ($country) {
            $address['country'] = $country['name'] ?? '';
        }

        return $address;
    }



    /* FETCHES */
    /**
     * Fetch employers from Staffology
     */
    public function fetchEmployers(): void
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new FetchEmployersJob([
            'description' => 'Fetching employers',
        ]));
    }



    /* SYNCS */
    /**
     * Checks if our database has employers that are deleted on staffology, if so, delete them on our system
     *
     * @param array $employers
     */
    public function syncEmployers(array $employers): void
    {
        $logger = new Logger();
        $logger->stdout('↧ Sync employers' . PHP_EOL, $logger::RESET);

        $hubEmployers = Employer::findAll();

        foreach ($hubEmployers as $hubEmp) {

            $exists = false;

            // loop through our employers and check if the employer is still on staffology
            foreach ($employers as $emp) {
                if ($emp['id'] === $hubEmp['staffologyId']) {
                    $exists = true;
                }
            }

            // remove the employer if it doesn't exists anymore
            if (!$exists) {
                $logger->stdout('✓ Delete employer ' . $hubEmp['name'] . PHP_EOL, $logger::FG_YELLOW);
                Craft::$app->getElements()->deleteElementById($hubEmp['id']);
            }
        }
    }



    /* SAVES */
    /**
     * @param array $employer
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function saveEmployer(array $employer): void
    {
        $logger = new Logger();
        $logger->stdout("✓ Save employer " . $employer['name'] ?? null . '...', $logger::RESET);

        $emp = Employer::findOne(['staffologyId' => $employer['id']]);

        if (!$emp) {
            $emp = new Employer();
        }

        $emp->siteId = Craft::$app->getSites()->currentSite->id;
        $emp->staffologyId = $employer['id'];
        $emp->name = $employer['name'] ?? null;
        $emp->title = $employer['name'] ?? null;
        $emp->logoUrl = $employer['logoUrl'] ?? null;
        $emp->crn = $employer['crn'] ?? null;
        $emp->startYear = $employer['startYear'] ?? null;
        $emp->currentYear = $employer['currentYear'] ?? null;
        $emp->defaultPayOptions = $employer['defaultPayOptions'] ?? null;
        $emp->employeeCount = $employer['employeeCount'] ?? null;

        $elementsService = Craft::$app->getElements();
        $success = $elementsService->saveElement($emp);

        if ($success) {
            $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);

            //Save relations (FKs)
            if ($employer['address'] ?? null) {
                Staff::$plugin->addresses->saveAddressByEmployer($employer['address'], $emp->id);
            }

            if ($employer['defaultPayOptions'] ?? null) {
                Staff::$plugin->payOptions->savePayOptionsByEmployer($employer['defaultPayOptions'], $emp->id);
            }
        } else {
            $logger->stdout(" failed" . PHP_EOL, $logger::FG_RED);

            $errors = "";

            foreach ($emp->errors as $err) {
                $errors .= implode(',', $err);
            }

            $logger->stdout($errors . PHP_EOL, $logger::FG_RED);
            Craft::error($emp->errors, __METHOD__);
        }
    }

    /**
     * @param array $hmrcDetails
     * @param int|null $hmrcDetailsId
     * @return HmrcDetails
     * @throws Exception
     */
    public function saveHmrcDetails(array $hmrcDetails, int $hmrcDetailsId = null): HmrcDetails
    {
        if ($hmrcDetailsId) {
            $record = HmrcDetails::findOne($hmrcDetailsId);

            if (!$record) {
                throw new Exception('Invalid hmrc details ID: ' . $hmrcDetailsId);
            }
        } else {
            $record = new HmrcDetails();
        }

        $record->officeNumber = SecurityHelper::encrypt($hmrcDetails['officeNumber'] ?? '');
        $record->payeReference = SecurityHelper::encrypt($hmrcDetails['payeReference'] ?? '');
        $record->accountsOfficeReference = SecurityHelper::encrypt($hmrcDetails['accountsOfficeReference'] ?? '');
        $record->employmentAllowance = $hmrcDetails['employmentAllowance'] ?? '';
        $record->employmentAllowanceMaxClaim = SecurityHelper::encrypt($hmrcDetails['employmentAllowanceMaxClaim'] ?? '');
        $record->apprenticeshipLevyAllowance = SecurityHelper::encrypt($hmrcDetails['apprenticeshipLevyAllowance'] ?? '');
        $record->quarterlyPaymentSchedule = $hmrcDetails['quarterlyPaymentSchedule'] ?? '';
        $record->includeEmploymentAllowanceOnMonthlyJournal = $hmrcDetails['includeEmploymentAllowanceOnMonthlyJournal'] ?? '';
        $record->carryForwardUnpaidLiabilities = $hmrcDetails['carryForwardUnpaidLiabilities'] ?? '';

        $record->save();

        return $record;
    }



    /* PARSE SECURITY VALUES */
    /**
     * @param array $employer
     * @return array
     */
    public function parseEmployer(array $employer): array
    {
        $employer['name'] = $employer['name'] ?? '';
        $employer['crn'] = SecurityHelper::decrypt($employer['crn'] ?? '');
        $employer['slug'] = $employer['slug'] ?? '';
        $employer['logoUrl'] = $employer['logoUrl'] ?? '';

        return $employer;
    }
}
