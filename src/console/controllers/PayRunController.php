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
        Staff::$plugin->payRuns->fetchPayRunByInternalEmployer($this->employer, $this->taxYear);
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
