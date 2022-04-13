<?php

namespace percipiolondon\staff\services;

use Craft;
use craft\base\Component;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\helpers\Security as SecurityHelper;
use percipiolondon\staff\records\PayLine as PayLineRecord;
use percipiolondon\staff\records\PayOption as PayOptionRecord;
use yii\db\Exception;

/**
 * Class PayOptions
 *
 * @package percipiolondon\staff\services
 */
class PayOptions extends Component
{
    /**
     * @param array $payOptions
     * @param int|null $employerId
     * @return PayOptionRecord
     * @throws \yii\db\StaleObjectException
     */
    public function savePayOptionsByEmployer(array $payOptions, int $employerId = null): PayOptionRecord
    {
        $record = PayOptionRecord::findOne(['employerId' => $employerId]);

        if (!$record) {
            $record = new PayOptionRecord();
        }

        $record->employerId = $employerId;

        return $this->_saveRecord($record, $payOptions);
    }

    /**
     * @param array $payLine
     * @param int|null $payOptionsId
     */
    public function savePayLines(array $payLine, int $payOptionsId = null): void
    {
        $record = PayLineRecord::findOne(['payOptionsId' => $payOptionsId, 'code' => $payLine['code'] ?? null]);

        if (!$record) {
            $record = new PayLineRecord();
        }

        $record->payOptionsId = $payOptionsId ?? null;
        $record->value = SecurityHelper::encrypt($payLine['value'] ?? '');
        $record->rate = SecurityHelper::encrypt($payLine['rate'] ?? '');
        $record->description = $payLine['description'] ?? null;
        $record->attachmentOrderId = $payLine['attachmentOrderId'] ?? null;
        $record->pensionId = $payLine['pensionId'] ?? null;
        $record->code = $payLine['code'] ?? null;

        $record->save();
    }

    /**
     * @param array $fpsFields
     * @param int|null $fpsFieldsId
     * @return FpsFields
     * @throws Exception
     */
    public function saveFpsFields(array $fpsFields, int $fpsFieldsId = null): FpsFields
    {
        if ($fpsFieldsId) {
            $record = FpsFields::findOne($fpsFieldsId);

            if (!$record) {
                throw new Exception('Invalid fps fields ID: ' . $fpsFieldsId);
            }
        } else {
            $record = new FpsFields();
        }

        $record->offPayrollWorker = $fpsFields['offPayrollWorker'] ?? null;
        $record->irregularPaymentPattern = $fpsFields['irregularPaymentPattern'] ?? null;
        $record->nonIndividual = $fpsFields['nonIndividual'] ?? null;
        $record->hoursNormallyWorked = $fpsFields['hoursNormallyWorked'] ?? null;

        $record->save();

        return $record;
    }

    /**
     * @param array $payOptions
     * @return array
     */
    public function parsePayOptions(array $payOptions): array
    {
        $payOptions['payAmount'] = SecurityHelper::decrypt($payOptions['payAmount'] ?? '');
        $payOptions['baseHourlyRate'] = SecurityHelper::decrypt($payOptions['baseHourlyRate'] ?? '');

        return $payOptions;
    }

    /**
     * @param array $payLine
     * @return array
     */
    public function parsePayLines(array $payLine): array
    {
        $payLine['value'] = SecurityHelper::decrypt($payLine['value'] ?? '');
        $payLine['rate'] = SecurityHelper::decrypt($payLine['rate'] ?? '');

        return $payLine;
    }

    /**
     * @param PayOptionRecord $record
     * @return PayOptionRecord|null
     * @throws \yii\db\StaleObjectException
     */
    private function _saveRecord(PayOptionRecord $record, array $payOptions): ?PayOptionRecord
    {
        $record->period = $payOptions['period'] ?? null;
        $record->ordinal = $payOptions['ordinal'] ?? null;
        $record->payAmount = SecurityHelper::encrypt($totals['payAmount'] ?? '');
        $record->basis = $payOptions['basis'] ?? 'Monthly';
        $record->nationalMinimumWage = $payOptions['nationalMinimumWage'] ?? null;
        $record->payAmountMultiplier = $payOptions['payAmountMultiplier'] ?? null;
        $record->baseHourlyRate = SecurityHelper::encrypt($totals['baseHourlyRate'] ?? '0');
        $record->autoAdjustForLeave = $payOptions['autoAdjustForLeave'] ?? null;
        $record->method = $payOptions['method'] ?? null;
        $record->payCode = $payOptions['payCode'] ?? null;
        $record->withholdTaxRefundIfPayIsZero = $payOptions['withholdTaxRefundIfPayIsZero'] ?? null;
        $record->mileageVehicleType = $payOptions['mileageVehicleType'] ?? null;
        $record->mapsMiles = $payOptions['mapsMiles'] ?? null;

        $success = $record->save();

        if ($success) {

            //delete pay lines from DB to prevent removed ones in staffology to still exists here
            $payLines = PayLineRecord::findAll(['payOptionsId' => $record->id]);
            foreach ($payLines as $payLine) {
                $payLine->delete();
            }

            //save pay lines
            foreach ($payOptions['regularPayLines'] ?? [] as $payLine) {
                $this->savePayLines($payLine, $record->id);
            }

            return $record;
        }

        $errors = '';
        foreach ($record->errors as $err) {
            $errors .= implode(',', $err);
        }

        $logger = new Logger();
        $logger->stdout($errors . PHP_EOL, $logger::FG_RED);
        Craft::error($record->errors, __METHOD__);

        return null;
    }
}
