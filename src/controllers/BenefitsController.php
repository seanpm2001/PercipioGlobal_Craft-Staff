<?php

namespace percipiolondon\staff\controllers;

use Craft;
use craft\elements\Asset;
use craft\web\Controller;
use percipiolondon\staff\elements\BenefitProvider;
use percipiolondon\staff\records\BenefitProvider as BenefitProviderRecord;
use percipiolondon\staff\Staff;
use yii\web\Response;

/**
 * Class BenefitsController
 *
 * @package percipiolondon\staff\controllers
 */
class BenefitsController extends Controller
{
    /**
     * @var string[]
     */
    protected $allowAnonymous = [];

    /**
     * Benefit Providers display
     *
     * @return Response The rendered result
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionBenefitsProvider(): Response
    {
        $this->requireLogin();

        $variables = [];

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Benefit Providers');

        $variables['controllerHandle'] = 'benefits';
        $variables['pluginName'] = Staff::$settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';
        $variables['providers'] = BenefitProvider::findAll();

        // Render the template
        return $this->renderTemplate('staff-management/benefits/provider/index', $variables);
    }

    /**
     * Benefit Providers display
     *
     * @return Response The rendered result
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionBenefitsProviderEdit(int $providerId = null): Response
    {
        $this->requireLogin();

        $provider = null;

        if($providerId) {
            $provider = BenefitProvider::findOne($providerId);
        }

        $variables = [];

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Benefit Providers');

        $variables['controllerHandle'] = 'benefits';
        $variables['pluginName'] = Staff::$settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';
        $variables['provider'] = $provider;
        $variables['volume'] = $this->_getVolume();
        $variables['errors'] = null;

        // Render the template
        return $this->renderTemplate('staff-management/benefits/provider/form', $variables);
    }

    /**
     * @return Response
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionBenefitsProviderSave(): Response
    {
        $this->requireLogin();
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $elementsService = Craft::$app->getElements();
        $success = false;

        $providerId = $request->getBodyParam('providerId');
        $provider = BenefitProvider::findOne($providerId);
        $savedProvider = null;

        if(is_null($provider)) {
            $provider = new BenefitProvider();
        }

        $provider->name = $request->getBodyParam('name') ?? '';
        $provider->logo = is_array($request->getBodyParam('logo')) ? (int)$request->getBodyParam('logo')[0] : null;
        $provider->url = $request->getBodyParam('url') ?? '';
        $provider->content = $request->getBodyParam('content') ?? '';

        if( $provider->validate() ) {
            $success = $elementsService->saveElement($provider);
            $savedProvider = BenefitProviderRecord::findOne($provider->id);
        }

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Benefit Providers');
        $variables = [];
        $variables['controllerHandle'] = 'benefits';
        $variables['pluginName'] = $pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';
        $variables['provider'] = $savedProvider;
        $variables['volume'] = $this->_getVolume();
        $variables['errors'] = $provider->getErrors();

        // Render the template
        if($success) {
            return $this->renderTemplate('staff-management/benefits/provider/detail', $variables);
        }

        return $this->renderTemplate('staff-management/benefits/provider/form', $variables);
    }

    /**
     * Group Benefits Provider Detail display
     *
     * @return Response The rendered result
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionBenefitsProviderDetail(int $providerId): Response
    {
        $this->requireLogin();

        $provider = BenefitProvider::findOne($providerId);

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Benefit Providers');

        $variables = [];
        $variables['controllerHandle'] = 'benefits';
        $variables['pluginName'] = Staff::$settings->pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';
        $variables['provider'] = $provider;

        // Render the template
        return $this->renderTemplate('staff-management/benefits/provider/detail', $variables);
    }

    /**
     * @return string|null
     */
    private function _getVolume(): ?string {
        $volume = null;

        foreach(Asset::sources() as $source) {
            if($source['data']['volume-handle'] == 'branding') {
                $volume = $source['key'];
            }
        }

        return $volume;
    }
}