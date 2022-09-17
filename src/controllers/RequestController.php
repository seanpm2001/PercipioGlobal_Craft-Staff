<?php

namespace percipiolondon\staff\controllers;

use Craft;
use craft\web\Controller;
use percipiolondon\staff\elements\Request;
use percipiolondon\staff\Staff;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class RequestController
 *
 * @package percipiolondon\staff\controllers
 */
class RequestController extends Controller
{
    /**
     * @return Response
     */
    public function actionIndex(): Response
    {
        $this->requireLogin();

        $variables = [];

        $pluginName = Staff::$plugin->settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Requests');

        $variables['controllerHandle'] = 'requests';
        $variables['pluginName'] = Staff::$plugin->settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'requests';

        // Render the template
        return $this->renderTemplate('staff-management/requests/index', $variables);
    }

    /**
     * @param int $requestId
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDetail(int $requestId): Response
    {
        $this->requireLogin();

        $request = Request::findOne($requestId);

        if (!$request) {
            throw new NotFoundHttpException();
        }

        $variables = [];

        $pluginName = Staff::$plugin->settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Requests');

        $variables['controllerHandle'] = 'requests';
        $variables['pluginName'] = Staff::$plugin->settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle} - {$request->id}";
        $variables['selectedSubnavItem'] = 'requests';
        $variables['employee'] = Staff::$plugin->employees->parseEmployee($request['employee']);

        $variables['request'] = $request;

        // Render the template
        return $this->renderTemplate('staff-management/requests/detail', $variables);
    }

    public function actionUndo(int $requestId): Response
    {
        $request = Request::findOne($requestId);

        if (!$request) {
            throw new NotFoundHttpException();
        }

        if ($request->status !== 'pending' && $request->status !== 'approved') {
            $request->status = 'pending';
            Craft::$app->getElements()->saveElement($request);
        }

        return Craft::$app->response->redirect(['staff-management/request/detail', 'requestId' => $requestId]);
    }
}