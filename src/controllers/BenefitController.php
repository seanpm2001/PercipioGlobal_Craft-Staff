<?php

namespace percipiolondon\staff\controllers;

use Craft;
use craft\web\Controller;
use percipiolondon\staff\records\BenefitPolicy;
use percipiolondon\staff\records\BenefitType;
use percipiolondon\staff\elements\Employer;
use percipiolondon\staff\Staff;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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

    public function actionPolicy(int $employerId, int $policyId): Response
    {
        $this->requireLogin();

        $employer = Employer::findOne($employerId);
        $policy = BenefitPolicy::findOne($policyId);

        if(is_null($employer) || is_null($policy)) {
            throw new NotFoundHttpException('Employer does not exist');
        }

        $variables = [];

        $pluginName = Staff::$settings->pluginName;
        $templateTitle = Craft::t('staff-management', 'Policy "' . $policy->policyName . '"');

        $variables['pluginName'] = $pluginName;
        $variables['title'] = $templateTitle;
        $variables['docTitle'] = "{$pluginName} - {$templateTitle}";
        $variables['selectedSubnavItem'] = 'benefits';
        $variables['employer'] = $employer;
        $variables['policy'] = $policy;

        // Render the template
        return $this->renderTemplate('staff-management/benefits/policy', $variables);
    }

    public function actionPolicyEdit(int $employerId, int $policyId): Response
    {
        Craft::dd("edit");
    }

    public function actionPolicyAdd(int $employerId, int $policyId): Response
    {
        Craft::dd('add');
    }
}