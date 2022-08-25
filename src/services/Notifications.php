<?php

namespace percipiolondon\staff\services;

use Craft;
use craft\base\Component;
use craft\elements\User;
use craft\helpers\App;
use craft\helpers\Template;
use craft\mail\Message;
use percipiolondon\staff\elements\Employee;
use percipiolondon\staff\elements\SettingsEmployee;
use percipiolondon\staff\elements\Notification as NotificationsElement;
use percipiolondon\staff\records\Settings;
use percipiolondon\staff\Staff;
use yii\helpers\Markdown;

/**
 * Class Notification
 *
 * @package percipiolondon\staff\services
 */
class Notifications extends Component
{
    /**
     * @param int $employeeId
     * @param string $type
     * @param string $notificationMessage
     * @param string|null $emailMessage
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    public function createNotification(int $employeeId, string $type, string $notificationMessage, string $emailMessage = null): void
    {
        $setting = Settings::findOne(['name' => 'notifications:'.$type]);
        $employee = Employee::findOne($employeeId);

        // save notification for the app
        if ($employee) {
            $notification = new NotificationsElement();
            $notification->employeeId = $employee->id;
            $notification->employerId = $employee->employerId;
            $notification->message = $notificationMessage;
            $notification->type = $type;

            $elementsService = Craft::$app->getElements();
            $elementsService->saveElement($notification);
        }

        // check if an email need to be send
        if ($setting && $employee && SettingsEmployee::findOne(['settingsId' => $setting->id, 'employeeId' => $employeeId])) {
            // approval to send notifications
            $user = User::findOne($employee->userId);

            if($user) {
                $this->_sendNotification($user, $employeeId, $emailMessage);
            }
        }
    }

    /**
     * @param int $notificationId
     * @param bool $viewed
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    public function updateNotificationViewed(int $notificationId, bool $viewed): void
    {
        $notification = NotificationsElement::findOne($notificationId);

        if ($notification) {
            $notification->viewed = $viewed;

            $elementsService = Craft::$app->getElements();
            $elementsService->saveElement($notification);
        }
    }

    /**
     * @param string $email
     * @param string $body
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \yii\base\Exception
     */
    private function _sendNotification(User $user, int $employeeId, string $body): void
    {
        $settings = (new \craft\services\ProjectConfig)->get('email');
        $message = new Message();
        $message->setFrom([App::parseEnv($settings['fromEmail']) => App::parseEnv($settings['fromName'])]);
        $message->setTo($user->email);
        $message->setSubject('Notification on the Harding Hub');

        $personalDetails = Staff::$plugin->employees->getPersonalDetailsByEmployee($employeeId, true);

        $view = Craft::$app->getView();
        $name = $personalDetails['firstName'] . ' ' . $personalDetails['lastName'];
        $content = Craft::t('staff-management', $body, ['name' => $name], 'en');
        $message->setHtmlBody($view->renderTemplate($settings['template'], array_merge([], [
            'body' => Template::raw(Markdown::process($content)),
        ]), 'site'));

        Craft::$app->getMailer()->send($message);
    }
}
