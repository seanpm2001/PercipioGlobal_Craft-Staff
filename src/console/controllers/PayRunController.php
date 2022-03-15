<?php
/**
 * staff-management plugin for Craft CMS 3.x
 *
 * Craft Staff Management provides an HR solution for payroll and benefits
 *
 * @link      http://percipio.london
 * @copyright Copyright (c) 2021 Percipio
 */

namespace percipiolondon\staff\console\controllers;

use craft\helpers\App;
use craft\queue\QueueInterface;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\records\Employer;
use percipiolondon\staff\Staff;

use Craft;
use craft\console\Controller;
use yii\queue\redis\Queue as RedisQueue;

/**
 * PayRunController Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Percipio
 * @package   Staff
 * @since     1.0.0-alpha.1
 */
class PayRunController extends Controller
{

    public $taxYear = '';
    public $employer = '';

    public function options($actionID)
    {
        $options = parent::options($actionID);
        $options[] = 'taxYear';
        $options[] = 'employer';

        return $options;
    }

    public function actionFetchPayRunByEmployer()
    {
        $logger = new Logger();

        if(!$this->employer) {
            $logger->stdout("There's no employer id provided" . PHP_EOL, $logger::FG_RED);
            Craft::error("There's no employer id provided", __METHOD__);
        }

        $employer = Staff::$plugin->employers->getEmployerById($this->employer);

        if($employer){

            $id = $employer['staffologyId'] ?? '';
            $taxYear = $this->taxYear === '' ? $employer['currentYear'] : $this->taxYear;
            $payPeriod = $employer['defaultPayOptions']['period'] ?? 'Monthly';

            $url = '/employers/'.$id.'/payrun/'.$taxYear.'/'.$payPeriod;

            $api = App::parseEnv(Staff::$plugin->getSettings()->apiKeyStaffology);
            $credentials = base64_encode('staff:'.$api);
            $headers = [
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials,
                ],
            ];
            $client = new \GuzzleHttp\Client();

            try {

                $response =  $client->get("https://api.staffology.co.uk/" . $url, $headers);
                $payRunData = json_decode($response->getBody()->getContents(), true);

                if($payRunData) {

                    $employer['id'] = $employer['staffologyId'];

                    Staff::$plugin->payRuns->fetchPayCodesList($employer);
                    Staff::$plugin->payRuns->fetchPayRuns($payRunData, $employer);

                    App::maxPowerCaptain();
                    $queue = Craft::$app->getQueue();
                    if ($queue instanceof QueueInterface) {
                        $queue->run();
                    } elseif ($queue instanceof RedisQueue) {
                        $queue->run(false);
                    }
                }

            } catch (\Exception $e) {

                $logger->stdout(PHP_EOL, $logger::RESET);
                $logger->stdout($e->getMessage() . PHP_EOL, $logger::FG_RED);
                Craft::error($e->getMessage(), __METHOD__);

            }
        }

    }
//    public function actionFetch()
//    {
//        return  Staff::$plugin->payRuns->fetch();
//    }
//
//    public function actionFetchPayslips()
//    {
//        return  Staff::$plugin->payRuns->fetchPayslips();
//    }
}
