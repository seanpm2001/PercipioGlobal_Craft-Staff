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

use craft\console\Controller;
use percipiolondon\staff\Staff;

/**
 * EmployerController Controller
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
class EmployerController extends Controller
{
    /**
     * Fetch all the employers from staffology
     * e.g.: actions/staff-management/employer-controller/fetch
     *
     * @return mixed
     */
    public function actionFetch()
    {
        return Staff::$plugin->employers->fetch();
    }
}
