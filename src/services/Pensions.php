<?php

namespace percipiolondon\staff\services;

use craft\base\Component;
use percipiolondon\staff\helpers\Logger;
use percipiolondon\staff\jobs\FetchPensionSchemesJob;
use percipiolondon\staff\jobs\CreatePensionJob;
use Craft;
use percipiolondon\staff\records\PensionSummary;
use percipiolondon\staff\records\WorkerGroup;

class Pensions extends Component
{

    /* GETTERS */

    public function getPensionSummaryById(int $pensionSummaryId): array
    {
        $pensionSummary = PensionSummary::findOne($pensionSummaryId);

        if(!$pensionSummary){
            return [];
        }

        $pensionSummary = $pensionSummary->toArray();

        // worker group
        $workerGroup = WorkerGroup::findOne($pensionSummary['workerGroupId']);
        if ($workerGroup) {
            $workerGroup = $workerGroup->toArray();
            $pensionSummary['workerGroup'] = $workerGroup;
        }

        return $pensionSummary;
    }


    /* FETCHES */
    public function fetchPension(array $employee, string $employer)
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new CreatePensionJob([
            'description' => 'Fetch pension schemes',
            'criteria' => [
                'employee' => $employee,
                'employer' => $employer
            ]
        ]));
    }

    public function fetchPensionSchemes(array $employers)
    {
        $queue = Craft::$app->getQueue();
        $queue->push(new FetchPensionSchemesJob([
            'description' => 'Fetch pension schemes',
            'criteria' => [
                'employers' => $employers,
            ]
        ]));
    }


    /* SAVES */
    public function savePension(array $pension)
    {
        $logger = new Logger();
        $logger->stdout("✓ Save pension ...", $logger::RESET);
        $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);
    }

    public function savePensionScheme(array $pensionScheme)
    {
        $logger = new Logger();
        $logger->stdout("✓ Save pension scheme ...", $logger::RESET);
        $logger->stdout(" done" . PHP_EOL, $logger::FG_GREEN);
    }
}
