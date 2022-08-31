<?php

namespace percipiolondon\staff\controllers;

use Craft;
use craft\helpers\DateTimeHelper;
use craft\helpers\Db;
use craft\web\Controller;
use percipiolondon\staff\elements\BenefitProvider;
use percipiolondon\staff\helpers\variants\VariantDental;
use percipiolondon\staff\records\BenefitPolicy;
use percipiolondon\staff\records\BenefitType;
use percipiolondon\staff\elements\Employer;
use percipiolondon\staff\records\BenefitVariantDental;
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

        $benefit = BenefitType::findOne($policy->benefitTypeId);
        $variants = match ($benefit->name ?? '') {
            'Dental' => BenefitVariantDental::find(['policyId' => $policyId])
                ->alias('variant')
                ->select('variant.*, title')
                ->leftJoin('staff_benefit_trs', 'staff_benefit_trs.id = variant.id')
                ->all(),
            default => []
        };

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Policy "' . $policy->policyName . '"');

        $variables = [];
        $variables['pluginName'] = $pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';
        $variables['employer'] = $employer;
        $variables['policy'] = $policy;
        $variables['variants'] = $variants;

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

        $variables = [];

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Add Policy');

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

        $variables = [];

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Add Policy');

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

    public function actionVariantAdd(int $policyId): Response
    {
        $this->requireLogin();

        $policy = BenefitPolicy::findOne($policyId);

        if(is_null($policy)) {
            throw new NotFoundHttpException('Policy does not exist');
        }

        $variables = [];

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Add Variant');

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

        $benefitType = BenefitType::findOne($policy->benefitTypeId);

        // save TRS
        if ($trsId) {
            $trs = TotalRewardsStatement::findOne($trsId);
        } else {
            $trs = new TotalRewardsStatement();
        }

        $trs->title = $request->getBodyParam('trsTitle');
        $trs->monetaryValue = $request->getBodyParam('trsMonetaryValue');
        $trs->startDate = Db::prepareDateForDb($request->getBodyParam('trsStartDate'));
        $trs->endDate = Db::prepareDateForDb($request->getBodyParam('trsEndDate'));

        $successTrs = $trs->save();

        $variant = match ($benefitType->name ?? '') {
            "Dental" => VariantDental::saveVariant($variantId == '' ? null : (int)$variantId, $successTrs ? $trs->id : null, $policy->id, $request),
        };

        $successVariant = $variant->save();

        if ($successTrs && $successVariant) {
            return $this->redirect('/admin/staff-management/benefits/employers/' . $policy->employerId . '/policy/' . $policy->id);
        }


        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', ($variantId ? 'Edit Variant' : 'Add Variant'));

        $variables['pluginName'] = $pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';
        $variables['policy'] = $policy;
        $variables['employer'] = Employer::findOne($policy->employerId);
        $variables['benefitType'] = BenefitType::findOne($policy->benefitTypeId);
        $variables['trs'] = $trs;
        $variables['variant'] = $variant;
        $variables['errors'] = $variant->getErrors();
        $variables['errorsTrs'] = $trs->getErrors();

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