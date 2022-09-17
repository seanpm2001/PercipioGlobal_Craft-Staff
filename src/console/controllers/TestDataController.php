<?php

namespace percipiolondon\staff\console\controllers;

use Craft;
use craft\console\Controller;
use craft\errors\ElementNotFoundException;
use percipiolondon\staff\elements\Employee;
use percipiolondon\staff\elements\Employer;
use percipiolondon\staff\elements\PayRun;
use percipiolondon\staff\elements\PayRunEntry;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\records\PayCode;
use yii\base\Exception;

/**
 * Class TestDataController
 *
 * @package percipiolondon\staff\console\controllers
 */
class TestDataController extends Controller
{
    /**
     * Provide a test employer / employee / paycodes / pay run and pay run entry
     */
    public function actionIndex(): void
    {
        try {
            $this->_createEmployer();
            $this->_createEmployee();
            $this->_createPayCode();
            $this->_createPayRun();
            $this->_createPayRunEntry();
        } catch (ElementNotFoundException | Exception | \Throwable $e) {
            Craft::error($e->getMessage(), __METHOD__);
        }
    }

    /**
     * Provide a test employer
     */
    public function actionTestEmployer(): void
    {
        try {
            $this->_createEmployer();
        } catch (ElementNotFoundException | Exception | \Throwable $e) {
            Craft::error($e->getMessage(), __METHOD__);
        }
    }

    /**
     * Provide a test employee
     */
    public function actionTestEmployee(): void
    {
        try {
            $this->_createEmployee();
        } catch (ElementNotFoundException | Exception | \Throwable $e) {
            Craft::error($e->getMessage(), __METHOD__);
        }
    }

    /**
     * @return void
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    private function _createEmployer(): void
    {
        $logger = new Logger();
        $logger->stdout('✓ Save employer Test company...', $logger::RESET);

        $testEmployer = Employer::findOne(['staffologyId' => '123']);

        if (!$testEmployer) {
            $testEmployer = new Employer();
        }

        $testEmployer->siteId = Craft::$app->getSites()->currentSite->id;
        $testEmployer->staffologyId = '123';
        $testEmployer->name = 'Test company';
        $testEmployer->logoUrl = '1';
        $testEmployer->crn = 'crn';
        $testEmployer->startYear = 'startYear';
        $testEmployer->currentYear = 'currentYear';
        $testEmployer->defaultPayOptions = null;
        $testEmployer->employeeCount = 'employeeCount';

        $elementsService = Craft::$app->getElements();
        $success = $elementsService->saveElement($testEmployer);

        if ($success) {
            $logger->stdout(' done' . PHP_EOL, $logger::FG_GREEN);

            return;
        }

        $logger->stdout(' failed' . PHP_EOL, $logger::FG_RED);
    }

    /**
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    private function _createEmployee(): void
    {
        $logger = new Logger();
        $logger->stdout('✓ Save employee Test...', $logger::RESET);

        $acmeEmployer = Employer::findOne(['staffologyId' => '64c5de94-7462-4bc6-a0e8-86a1bd52277d']);

        $testEmployee = Employee::findOne(['staffologyId' => '123']);

        if (!$testEmployee) {
            $testEmployee = new Employee();
        }

        $testEmployee->employerId = $acmeEmployer['id'] ?? null;
        $testEmployee->staffologyId = '123';
        $testEmployee->siteId = Craft::$app->getSites()->currentSite->id;
        $testEmployee->status = 1;
        $testEmployee->personalDetailsObject = [];
        $testEmployee->niNumber = '123456';
        $testEmployee->userId = null;
        $testEmployee->isDirector = false;

        $elementsService = Craft::$app->getElements();
        $success = $elementsService->saveElement($testEmployee);

        if ($success) {
            $logger->stdout(' done' . PHP_EOL, $logger::FG_GREEN);
        } else {
            $logger->stdout(' failed' . PHP_EOL, $logger::FG_RED);
        }
    }

    /**
     *
     */
    private function _createPayCode(): void
    {
        $logger = new Logger();
        $logger->stdout('✓ Save pay code Test...', $logger::RESET);

        $acmeEmployer = Employer::findOne(['staffologyId' => 'e303dcbe-1b46-4639-9b54-dc5328e648cf']);

        $testPayCode = PayCode::findOne(['code' => '123']);

        if (!$testPayCode) {
            $testPayCode = new PayCode();
        }

        $testPayCode->title = 'Test';
        $testPayCode->code = '123';
        $testPayCode->employerId = $acmeEmployer->id ?? null;
        $testPayCode->defaultValue = '123';
        $testPayCode->isSystemCode = false;

        $success = $testPayCode->save();

        if ($success) {
            $logger->stdout(' done' . PHP_EOL, $logger::FG_GREEN);
        } else {
            $logger->stdout(' failed' . PHP_EOL, $logger::FG_RED);
        }
    }

    /**
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    private function _createPayRun(): void
    {
        $logger = new Logger();
        $logger->stdout('✓ Save pay run test...', $logger::RESET);

        $acmeEmployer = Employer::findOne(['staffologyId' => 'e303dcbe-1b46-4639-9b54-dc5328e648cf']);

        $testPayRun = PayRun::findOne(['url' => '123']);

        if (!$testPayRun) {
            $testPayRun = new PayRun();
        }

        $testPayRun->employerId = $acmeEmployer->id ?? null;
        $testPayRun->taxYear = 'taxYear';
        $testPayRun->taxMonth = 'taxMonth';
        $testPayRun->payPeriod = 'payPeriod';
        $testPayRun->ordinal = 'ordinal';
        $testPayRun->period = 'period';
        $testPayRun->startDate = '2022-05-01 00:00:00';
        $testPayRun->endDate = '2022-05-01 00:00:00';
        $testPayRun->paymentDate = '2022-05-01 00:00:00';
        $testPayRun->employeeCount = 'employeeCount';
        $testPayRun->subContractorCount = 'subContractorCount';
        $testPayRun->state = 'state';
        $testPayRun->isClosed = 'isClosed';
        $testPayRun->dateClosed = null;
        $testPayRun->url = '123';

        $elementsService = Craft::$app->getElements();
        $success = $elementsService->saveElement($testPayRun);

        if ($success) {
            $logger->stdout(' done' . PHP_EOL, $logger::FG_GREEN);
        } else {
            $logger->stdout(' failed' . PHP_EOL, $logger::FG_RED);
        }
    }

    /**
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    private function _createPayRunEntry(): void
    {
        $logger = new Logger();
        $logger->stdout('✓ Save pay run entry test...', $logger::RESET);

        $acmeEmployer = Employer::findOne(['staffologyId' => 'e303dcbe-1b46-4639-9b54-dc5328e648cf']);

        $acmePayRun = PayRun::findAll(['employerId' => $acmeEmployer])[1];
        $testEmployee = Employee::findOne(['employerId' => $acmeEmployer['id']]);

        $testPayRunEntry = PayRunEntry::findOne(['staffologyId' => '123']);

        if(!$testPayRunEntry) {
            $testPayRunEntry = new PayRunEntry();
        }

        $testPayRunEntry->employerId = $acmeEmployer['id'] ?? null;
        $testPayRunEntry->employeeId = $testEmployee['id'] ?? null;
        $testPayRunEntry->payRunId = $acmePayRun['id'] ?? null;
        $testPayRunEntry->staffologyId = '123';
        $testPayRunEntry->taxYear = $payRunEntryData['taxYear'] ?? null;
        $testPayRunEntry->startDate = $payRunEntryData['startDate'] ?? null;
        $testPayRunEntry->endDate = $payRunEntryData['endDate'] ?? null;
        $testPayRunEntry->note = null;
        $testPayRunEntry->bacsSubReference = null;
        $testPayRunEntry->bacsHashcode = null;
        $testPayRunEntry->percentageOfWorkingDaysPaidAsNormal = null;
        $testPayRunEntry->workingDaysNotPaidAsNormal = null;
        $testPayRunEntry->payPeriod = null;
        $testPayRunEntry->ordinal = null;
        $testPayRunEntry->period = null;
        $testPayRunEntry->isNewStarter = null;
        $testPayRunEntry->unpaidAbsence = null;
        $testPayRunEntry->hasAttachmentOrders = null;
        $testPayRunEntry->paymentDate = null;
        $testPayRunEntry->forcedCisVatAmount = null;
        $testPayRunEntry->holidayAccrued =  null;
        $testPayRunEntry->state = null;
        $testPayRunEntry->isClosed = null;
        $testPayRunEntry->manualNi = null;
        $testPayRunEntry->payrollCodeChanged = null;
        $testPayRunEntry->aeNotEnroledWarning = null;
        $testPayRunEntry->receivingOffsetPay = null;
        $testPayRunEntry->paymentAfterLearning = null;
        $testPayRunEntry->pdf = '';

        $elementsService = Craft::$app->getElements();
        $success = $elementsService->saveElement($testPayRunEntry);

        if ($success) {
            $logger->stdout(' done' . PHP_EOL, $logger::FG_GREEN);
        } else {
            $logger->stdout(' failed' . PHP_EOL, $logger::FG_RED);
        }
    }
}