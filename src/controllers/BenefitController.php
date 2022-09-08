<?php

namespace percipiolondon\staff\controllers;

use Craft;
use craft\helpers\DateTimeHelper;
use craft\helpers\Db;
use craft\web\Controller;
use percipiolondon\staff\elements\BenefitProvider;
use percipiolondon\staff\elements\BenefitVariant;
use percipiolondon\staff\records\BenefitPolicy;
use percipiolondon\staff\records\BenefitType;
use percipiolondon\staff\elements\Employer;
use percipiolondon\staff\records\TotalRewardsStatement;
use percipiolondon\staff\Staff;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class BenefitController
 *
 * @package percipiolondon\staff\controllers
 */
class BenefitController extends Controller
{
    /**
     * Benefit Employer display
     *
     * @return Response The rendered result
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionIndex(): Response
    {
        $this->requireLogin();

        $variables = [];

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Employers');

        $variables['pluginName'] = $pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';

        // Render the template
        return $this->renderTemplate('staff-management/benefits/employers', $variables);
    }

    /**
     * Benefit Employer display
     *
     * @return Response The rendered result
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionDetail(int $employerId): Response
    {
        $this->requireLogin();

        $variables = [];

        $employer = Employer::findOne($employerId);

        if(is_null($employer)) {
            throw new NotFoundHttpException('Employer does not exist');
        }

        $benefitTypes = BenefitType::find()->orderBy(['name'=>SORT_ASC])->all();
        $benefits = [];

        foreach ($benefitTypes as $benefit) {
            $benefit = $benefit->toArray();
            $benefit['policies'] = BenefitPolicy::findAll(['benefitTypeId' => $benefit['id'], 'employerId' => $employer->id]);
            $benefits[] = $benefit;
        }

        usort($benefits, function($a, $b) {
            $countA = count($a['policies']);
            $countB = count($b['policies']);

            if($countA == $countB ) {
                return(0);
            }

            return (($countA > $countB) ? -1 : 1);
        });

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Benefits > ' . $employer['name']);

        $variables['pluginName'] = $pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';
        $variables['employer'] = $employer;
        $variables['benefits'] = $benefits;

        // Render the template
        return $this->renderTemplate('staff-management/benefits/employers/detail', $variables);
    }




    /* -------------------------------
     * POLICIES
     --------------------------------- */

    /**
     * @param int $employerId
     * @param int $policyId
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionPolicy(int $employerId, int $policyId): Response
    {
        $this->requireLogin();

        $employer = Employer::findOne($employerId);
        $policy = BenefitPolicy::findOne($policyId);

        if(is_null($employer) || is_null($policy)) {
            throw new NotFoundHttpException('Employer does not exist');
        }

        $policy->policyStartDate = DateTimeHelper::toDateTime($policy->policyStartDate)->format('jS M, Y');
        $policy->policyRenewalDate = DateTimeHelper::toDateTime($policy->policyRenewalDate)->format('jS M, Y');

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Policy "' . $policy->policyName . '"');

        $variables = [];
        $variables['pluginName'] = $pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';
        $variables['employer'] = $employer;
        $variables['policy'] = $policy;

        // Render the template
        return $this->renderTemplate('staff-management/benefits/policy', $variables);
    }

    /**
     * @param int $employerId
     * @param int $policyId
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionPolicyEdit(int $employerId, int $policyId): Response
    {
        $this->requireLogin();

        $employer = Employer::findOne($employerId);
        $policy = BenefitPolicy::findOne($policyId);

        if(is_null($employer) || is_null($policy)) {
            throw new NotFoundHttpException('Employer or benefit does not exist');
        }

        $benefit = BenefitType::findOne($policy->benefitTypeId);

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Add Policy');

        $variables = [];
        $variables['pluginName'] = $pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';
        $variables['employer'] = $employer;
        $variables['policy'] = $policy;
        $variables['benefit'] = $benefit;
        $variables['providers'] = $this->_getProviders();
        // Render the template
        return $this->renderTemplate('staff-management/benefits/policy/form', $variables);
    }

    /**
     * @param int $employerId
     * @param int $benefitTypeId
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionPolicyAdd(int $employerId, int $benefitTypeId): Response
    {
        $this->requireLogin();

        $employer = Employer::findOne($employerId);
        $benefit = BenefitType::findOne($benefitTypeId);

        if(is_null($employer) || is_null($benefit)) {
            throw new NotFoundHttpException('Employer or benefit does not exist');
        }

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Add Policy');

        $variables = [];
        $variables['pluginName'] = $pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';
        $variables['employer'] = $employer;
        $variables['benefit'] = $benefit;
        $variables['policy'] = null;
        $variables['providers'] = $this->_getProviders();

        // Render the template
        return $this->renderTemplate('staff-management/benefits/policy/form', $variables);
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionPolicySave(): Response
    {
        $this->requireLogin();
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $success = false;

        $policyId = $request->getBodyParam('policyId');

        $employer = Employer::findOne($request->getBodyParam('employerId'));
        $benefit = BenefitType::findOne($request->getBodyParam('benefitTypeId'));
        $provider = BenefitProvider::findOne($request->getBodyParam('providerId'));

        if(is_null($employer) || is_null($benefit) || is_null($provider)) {
            throw new NotFoundHttpException('Employer, provider or benefit does not exist');
        }

        if ($policyId) {
            $policy = BenefitPolicy::findOne($policyId);
        } else {
            $policy = new BenefitPolicy();
        }

        $policy->providerId = $provider->id;
        $policy->benefitTypeId = $benefit->id;
        $policy->employerId = $employer->id;
        $policy->internalCode = $request->getBodyParam('internalCode');
        $policy->status = $request->getBodyParam('status');
        $policy->policyName = $request->getBodyParam('policyName');
        $policy->policyNumber = $request->getBodyParam('policyNumber');
        $policy->policyHolder = $request->getBodyParam('policyHolder');
        $policy->policyStartDate = Db::prepareDateForDb($request->getBodyParam('policyStartDate'));
        $policy->policyRenewalDate = Db::prepareDateForDb($request->getBodyParam('policyRenewalDate'));
        $policy->paymentFrequency = $request->getBodyParam('paymentFrequency');
        $policy->commissionRate = $request->getBodyParam('commissionRate') ? (float)$request->getBodyParam('commissionRate') : null;
        $policy->description = $request->getBodyParam('description');

        if( $policy->validate() ) {
            $success = $policy->save();
        }

        if($success) {
            return $this->redirect('/admin/staff-management/benefits/employers/' . $employer->id);
        }

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', ($policyId ? 'Edit Policy' : 'Add Policy'));

        $variables = [];
        $variables['pluginName'] = $pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';
        $variables['policy'] = $policy;
        $variables['employer'] = $employer;
        $variables['benefit'] = $benefit;
        $variables['providers'] = $this->_getProviders();
        $variables['errors'] = $policy->getErrors();

        // Render the template
        return $this->renderTemplate('staff-management/benefits/policy/form', $variables);
    }

    /**
     * @throws \yii\db\StaleObjectException
     * @throws NotFoundHttpException
     */
    public function actionPolicyDelete(int $policyId): Response
    {
        $policy = BenefitPolicy::findOne($policyId);

        if(is_null($policy)) {
            throw new NotFoundHttpException('Employer or benefit does not exist');
        }

        $employer = Employer::findOne($policy->employerId);

        $policy->delete();

        return $this->redirect('/admin/staff-management/benefits/employers/' . $employer->id);
    }




    /* -------------------------------
     * VARIANTS
     --------------------------------- */

    /**
     * @param int $variantId
     * @param int $policyId
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionVariant(int $variantId): Response
    {
        $this->requireLogin();

        $variant = BenefitVariant::findOne($variantId);

        if(is_null($variant)) {
            throw new NotFoundHttpException('Variant does not exist');
        }

        $policy = $variant->getPolicy();
        $trs = $variant->getTotalRewardsStatement();
        $benefitType = BenefitType::findOne($policy->benefitTypeId ?? null);

        $variantValues = $variant->toArray();
        $variantValues = array_merge($variantValues, $variant->getValues($benefitType->slug ?? ''));

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Variant');

        $variables = [];
        $variables['pluginName'] = $pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';
        $variables['variant'] = $variantValues;
        $variables['benefitType'] = $benefitType;
        $variables['policy'] = $policy;
        $variables['trs'] = $trs;

        // Render the template
        return $this->renderTemplate('staff-management/benefits/variant', $variables);
    }

    public function actionVariantAdd(int $policyId): Response
    {
        $this->requireLogin();

        $policy = BenefitPolicy::findOne($policyId);

        if(is_null($policy)) {
            throw new NotFoundHttpException('Policy does not exist');
        }

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Add Variant');

        $variables = [];
        $variables['pluginName'] = $pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';
        $variables['policy'] = $policy;
        $variables['employer'] = Employer::findOne($policy->employerId);
        $variables['benefitType'] = BenefitType::findOne($policy->benefitTypeId);
        $variables['trs'] = null;
        $variables['variant'] = null;
        $variables['errors'] = null;
        $variables['errorsTrs'] = null;

        // Render the template
        return $this->renderTemplate('staff-management/benefits/variant/form', $variables);
    }

    /**
     * @param int $employerId
     * @param int $policyId
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionVariantEdit(int $variantId): Response
    {
        $this->requireLogin();

        $variant = BenefitVariant::findOne($variantId);

        if(is_null($variant)) {
            throw new NotFoundHttpException('Variant does not exist');
        }

        $policy = $variant->getPolicy();
        $trs = $variant->getTotalRewardsStatement();
        $benefitType = BenefitType::findOne($policy->benefitTypeId ?? null);

        $variantValues = $variant->toArray();
        $variantValues = array_merge($variantValues, $variant->getValues($benefitType->slug ?? ''));

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Add Variant');

        $variables = [];
        $variables['pluginName'] = $pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';
        $variables['policy'] = $policy;
        $variables['employer'] = Employer::findOne($policy->employerId);
        $variables['benefitType'] = BenefitType::findOne($policy->benefitTypeId);
        $variables['trs'] = $trs;
        $variables['variant'] = $variantValues;
        $variables['errors'] = null;
        $variables['errorsTrs'] = null;

        // Render the template
        return $this->renderTemplate('staff-management/benefits/variant/form', $variables);
    }

    /**
     * @throws \yii\db\StaleObjectException
     * @throws NotFoundHttpException
     */
    public function actionVariantDelete(int $variantId): Response
    {
        $variant = BenefitVariant::findOne($variantId);

        if(is_null($variant)) {
            throw new NotFoundHttpException('Variant does not exist');
        }

        $policy = BenefitPolicy::findOne($variant->policyId);

        if(is_null($policy)) {
            throw new NotFoundHttpException('Policy does not exist');
        }

        Craft::$app->getElements()->deleteElementById($variant->id);

        return $this->redirect('/admin/staff-management/benefits/employers/' . $policy->employerId . '/policy/' . $policy->id);
    }

    public function actionVariantSave(): Response
    {
        $this->requireLogin();
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();

        $policyId = $request->getBodyParam('policyId');
        $trsId = $request->getBodyParam('trsId');
        $variantId = $request->getBodyParam('variantId');

        $policy = BenefitPolicy::findOne($policyId);

        if(is_null($policy)) {
            throw new NotFoundHttpException('Policy does not exist');
        }

        // save benefit variant
        $variant = BenefitVariant::findOne($variantId);

        if(is_null($variant)) {
            $variant = new BenefitVariant();
        }

        $variant->policyId = $policyId;
        $variant->name = $request->getBodyParam('name');
        $variant->request = $request;

        //@TODO: before save --> make sure the record that needs saving is valid
        $benefitType = BenefitType::findOne($policy->benefitTypeId);
        $variantFilled = $variant->getFilledVariant($benefitType->slug ?? '');
        $success = $variantFilled->validate() && Craft::$app->getElements()->saveElement($variant);

        // save TRS
        if ($trsId) {
            $trs = TotalRewardsStatement::findOne($trsId);
        } else {
            $trs = new TotalRewardsStatement();
        }
        $trs->variantId = $variant->id;
        $trs->title = $request->getBodyParam('trsTitle');
        $trs->monetaryValue = $request->getBodyParam('trsMonetaryValue');
        $trs->startDate = Db::prepareDateForDb($request->getBodyParam('trsStartDate'));
        $trs->endDate = Db::prepareDateForDb($request->getBodyParam('trsEndDate'));
        $successTrs = $trs->save();

        if ($success && $successTrs) {
            return $this->redirect('/admin/staff-management/benefits/variant/' . $variant->id);
        }

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', ($variantId ? 'Edit Variant' : 'Add Variant'));
        $benefitType = BenefitType::findOne($policy->benefitTypeId);

        $variables = [];
        $variables['pluginName'] = $pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';
        $variables['policy'] = $policy;
        $variables['employer'] = Employer::findOne($policy->employerId);
        $variables['benefitType'] = BenefitType::findOne($policy->benefitTypeId);
        $variables['trs'] = $trs;
        $variables['variant'] = $variant->getFields($benefitType->slug ?? '');
        $variables['errors'] = array_merge($variantFilled->getErrors(), $variant->getErrors());
        $variables['errorsTrs'] = $trs->getErrors();

//        Craft::dd($variables['variant']);

        // Render the template
        return $this->renderTemplate('staff-management/benefits/variant/form', $variables);
    }

    /**
     * @return array[]
     */
    private function _getProviders(): array {
        $providers = [[
            'label' => Craft::t('staff-management', 'Choose a Benefit Provider'),
            'value' => null,
        ]];

        foreach(BenefitProvider::findAll() as $provider){
            $providers[] = [
                'value' => $provider->id,
                'label' => $provider->name,
            ];
        }

        return $providers;
    }
}