<?php

namespace percipiolondon\staff\services;

use Craft;
use craft\base\Component;
use craft\elements\User;
use craft\events\UserEvent;
use craft\services\Users;
use percipiolondon\staff\helpers\HistoryMessages;
use percipiolondon\staff\elements\History as HistoryElement;
use percipiolondon\staff\records\Employee;
use yii\base\Event;
use yii\base\ModelEvent;

/**
 * Class History
 *
 * @package percipiolondon\staff\services
 */
class History extends Component
{
    // Public Methods
    // =========================================================================


    /* EVENTS */
    /**
     * Catch Craft events to save history logging from
     */
    public function catchEventListeners(): void
    {
        // save history when user gets activated
        Event::on(
            Users::class,
            Users::EVENT_AFTER_VERIFY_EMAIL,
            function (UserEvent $event) {
                $employee = Employee::findOne(['userId' => $event->user->id]);

                if ($employee) {
                   $this->saveHistory($employee, 'system', HistoryMessages::getMessage('system', 'user','activate'));
                }
            }
        );

        // save history when user sets new password
        Event::on(
            User::class,
            User::EVENT_BEFORE_VALIDATE,
            function(ModelEvent $event) {
                if ($event->sender->newPassword) {
                    $employee = Employee::findOne(['userId' => $event->sender->id]);

                    if ($employee) {
                        $this->saveHistory($employee, 'system', HistoryMessages::getMessage('system', 'user','set_password'));
                    }
                }
            }
        );
    }


    /* SAVES */
    /**
     * @param Employee $employee
     * @param string $type
     * @param string $message
     * @param string|null $data
     * @param int|null $administerId
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    public function saveHistory(Employee $employee, string $type, string $message, string $data = null, int $administerId = null): void
    {
        if ($employee) {
            $history = new HistoryElement();
            $history->type = $type;
            $history->employeeId = $employee->id;
            $history->employerId = $employee->employerId;
            $history->message = $message;
            $history->data = $data;
            $history->administerId = $administerId;

            Craft::$app->getElements()->saveElement($history);
        }
    }
}
