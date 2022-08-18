<?php

namespace percipiolondon\staff\services;

use Craft;
use craft\base\Component;
use craft\helpers\App;
use craft\helpers\Json;
use GuzzleHttp\Exception\GuzzleException;
use percipiolondon\staff\elements\Employee;
use percipiolondon\staff\elements\Employer;
use percipiolondon\staff\records\Requests as RequestRecord;
use percipiolondon\staff\Staff;

class Requests extends Component
{
    public function saveToStaffology(RequestRecord $request): void
    {
        match (true) {
            $request->type === 'address' => $this->_saveEmployee($request),
            $request->type === 'personal_details' => $this->_saveEmployee($request),
        };
    }

    private function _saveEmployee(RequestRecord $request): void
    {
        $employer = Employer::findOne($request->employerId);
        $employee = Employee::findOne($request->employeeId);

        if($employer && $employee) {
            $staffologyEmployer = $employer->staffologyId;
            $staffologyEmployee = $employee->staffologyId;
            $endpoint = '/employers/'.$staffologyEmployer.'/employees/'.$staffologyEmployee;

            $this->_sync($endpoint, $request->request);
        }
    }

    private function _sync(string $endpoint, string $json): bool
    {
        $api = App::parseEnv(Staff::$plugin->getSettings()->apiKeyStaffology);
        $base_url = 'https://api.staffology.co.uk'.$endpoint;
        $credentials = base64_encode('staff:' . $api);
        $client = new \GuzzleHttp\Client([
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
            ],
        ]);

        $json = json_decode($json);
        $data = json_decode(json_encode($json), true);

        try {
            $response = $client->put(
                $base_url,
                [
                    'json' => [
                        'personalDetails' => [
                            'middleName' => 'string',
                        ]
                    ],
                ]
            );

            $result = Json::decodeIfJson($response->getBody()->getContents(), true);
            Craft::dd($result);

            return true;
        } catch (GuzzleException $e) {
            Craft::error($e->getMessage(), __METHOD__);

            return false;
        }
    }
}