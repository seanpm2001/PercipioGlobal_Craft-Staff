<?php

namespace percipiolondon\staff\jobs;

use Craft;
use craft\helpers\App;
use craft\helpers\Json;
use craft\queue\BaseJob;
use percipiolondon\staff\helpers\HistoryMessages;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\helpers\NotificationMessage;
use percipiolondon\staff\Staff;

class CreateNotificationPaySlip extends BaseJob
{
    public $criteria;

    public function execute($queue): void
    {
        // create a history log
        $payslipData = [];
        $payslipData['paymentDate'] = $this->criteria['payRunEntry']->paymentDate;
        $payslipData['taxYear'] = $this->criteria['payRunEntry']->taxYear;
        $payslipData['startDate'] = $this->criteria['payRunEntry']->startDate;
        $payslipData['endDate'] = $this->criteria['payRunEntry']->endDate;
        $payslipData['period'] = $this->criteria['payRunEntry']->period;
        $payslipData['payRunTotals'] = $this->criteria['payRunTotals']->id ?? null;

        Staff::$plugin->history->saveHistory($this->criteria['employee'], 'payroll', HistoryMessages::getMessage('payroll', 'payslip'), json_encode($payslipData, JSON_THROW_ON_ERROR));

        // create a notification
        $notificationMessage = NotificationMessage::getNotification('payroll' , 'payslip');
        $emailMessage = NotificationMessage::getEmail('payroll' , 'payslip');
        Staff::$plugin->notifications->createNotificationByEmployee($this->criteria['employee']->id ?? null, 'payroll', true, $notificationMessage, $emailMessage);
    }
}
