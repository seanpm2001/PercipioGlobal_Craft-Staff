<?php

namespace percipiolondon\staff\services;

use Craft;
use craft\base\Component;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\records\PensionSummary;
use percipiolondon\staff\records\WorkerGroup;
use yii\db\Exception;

/**
 * Class Pensions
 *
 * @package percipiolondon\staff\services
 */
class Pensions extends Component
{

    /* GETTERS */
    /**
     * @param int $payRunEntryId
     * @return array
     */
    public function getPensionSummaryByPayRunEntryId(int $payRunEntryId): array
    {
        $pensionSummary = PensionSummary::findOne(['payRunEntryId' => $payRunEntryId]);

        if(!$pensionSummary){
            return [];
        }

        $pensionSummary = $pensionSummary->toArray();

        // worker group
        $workerGroup = WorkerGroup::findOne($pensionSummary['workerGroupId']);
        if ($workerGroup) {
            $workerGroup = $workerGroup->toArray();
            $pensionSummary['workerGroup'] = $workerGroup;
        }

        return $pensionSummary;
    }


    /* FETCHES */
    /**
     * @param array $employee
     * @param string $employer
     */
    public function fetchPension(array $employee, string $employer)
    {
        //@TODO
    }

    /**
     * @param array $employers
     */
    public function fetchPensionSchemes(array $employers)
    {
       //@TODO
    }


    /* SAVES */
    /**
     * @param array $pension
     */
    public function savePension(array $pension): void
    {
        $logger = new Logger();
        $logger->stdout("✓ Save pension ...", $logger::RESET);
        $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);
    }

    /**
     * @param array $pensionScheme
     */
    public function savePensionScheme(array $pensionScheme): void
    {
        $logger = new Logger();
        $logger->stdout("✓ Save pension scheme ...", $logger::RESET);
        $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);
    }

    /**
     * @param array $pensionSummary
     * @param int|null $payRunEntryId
     * @return PensionSummary
     * @throws Exception
     */
    public function savePensionSummary(array $pensionSummary, int $payRunEntryId = null): PensionSummary
    {
        $record = PensionSummary::findOne(['payRunEntryId' => $payRunEntryId]);

        if (!$record) {
            $record = new PensionSummary();
        }

        $workerGroupId = $record['workerGroupId'] ?? null;

        $workerGroup = $pensionSummary['workerGroup'] ? $this->saveWorkerGroup($pensionSummary['workerGroup'], $workerGroupId) : null;

        $record->payRunEntryId = $payRunEntryId;
        $record->workerGroupId = $workerGroup->id ?? null;
        $record->name = SecurityHelper::encrypt($pensionSummary['name'] ?? '');
        $record->startDate = SecurityHelper::encrypt($pensionSummary['startDate'] ?? '');
        $record->pensionRule = $pensionSummary['pensionRule'] ?? '';
        $record->employeePensionContributionMultiplier = SecurityHelper::encrypt($pensionSummary['employeePensionContributionMultiplier'] ?? '');
        $record->additionalVoluntaryContribution = SecurityHelper::encrypt($pensionSummary['additionalVoluntaryContribution'] ?? '');
        $record->avcIsPercentage = $fpsFields['avcIsPercentage'] ?? null;
        $record->autoEnrolled = $fpsFields['autoEnrolled'] ?? null;
        $record->papdisPensionProviderId = $fpsFields['papdisPensionProviderId'] ?? null;
        $record->papdisEmployerId = $fpsFields['papdisEmployerId'] ?? null;

        $record->save();

        return $record;
    }


    /**
     * @param array $workerGroup
     * @param int|null $workerGroupId
     * @return WorkerGroup
     * @throws Exception
     */
    public function saveWorkerGroup(array $workerGroup, int $workerGroupId = null): WorkerGroup
    {
        if ($workerGroupId) {
            $record = WorkerGroup::findOne($workerGroupId);

            if (!$record) {
                throw new Exception('Invalid worker group ID: ' . $workerGroupId);
            }
        } else {
            $record = new WorkerGroup();
        }

        $record->staffologyId = $workerGroup['workerGroupId'] ?? null;
        $record->name = SecurityHelper::encrypt($workerGroup['name'] ?? '');
        $record->contributionLevelType = $workerGroup['contributionLevelType'] ?? null;
        $record->employeeContribution = SecurityHelper::encrypt($workerGroup['employeeContribution'] ?? '');
        $record->employeeContributionIsPercentage = $workerGroup['employeeContributionIsPercentage'] ?? null;
        $record->employerContribution = SecurityHelper::encrypt($workerGroup['employerContribution'] ?? '');
        $record->employerContributionIsPercentage = $workerGroup['employerContributionIsPercentage'] ?? null;
        $record->employerContributionTopUpPercentage = SecurityHelper::encrypt($workerGroup['employerContributionTopUpPercentage'] ?? '');
        $record->customThreshold = $workerGroup['customThreshold'] ?? null;
        $record->lowerLimit = SecurityHelper::encrypt($workerGroup['lowerLimit'] ?? '');
        $record->upperLimit = SecurityHelper::encrypt($workerGroup['upperLimit'] ?? '');

        $record->save();

        return $record;
    }
}
