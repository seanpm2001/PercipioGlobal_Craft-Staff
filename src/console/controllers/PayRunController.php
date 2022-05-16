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

use Craft;

use craft\console\Controller;
use percipiolondon\staff\Staff;

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
    /**
     * Provide a specific tax year. If not provided, the current tax year will be used
     * @var string
     */
    public $taxYear = '';

    /**
     * Provide an employer to fetch [required]
     * @var string
     */
    public $employer = '';

    /**
     * @param string $actionID
     * @return int[]|string[]
     */
    public function options($actionID)
    {
        $options = parent::options($actionID);
        $options[] = 'taxYear';
        $options[] = 'employer';

        return $options;
    }

    /**
     * Fetch pay runs from an employer. If you want a specific tax year, you can provide the tax year. parameter examples: --employer='1234' --taxYear='2021'
     * e.g. staff-management/pay-run/fetch-pay-run-by-employer --employer="1234" --taxYear="2021"
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function actionFetchPayRunByEmployer()
    {
        Staff::$plugin->payRuns->fetchPayRunByEmployer($this->employer, $this->taxYear);
    }
}
