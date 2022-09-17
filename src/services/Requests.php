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

/**
 * Class Requests
 *
 * @package percipiolondon\staff\services
 */
class Requests extends Component
{
    // Public Methods
    // =========================================================================


    /* SAVES */
    /**
     * @param RequestRecord $request
     * @return bool
     */
    public function saveToStaffology(RequestRecord $request): bool
    {
        return match (true) {
            $request->type === 'address' => $this->_syncEmployee($request),
            $request->type === 'personal_details' => $this->_syncEmployee($request),
            $request->type === 'telephone' => $this->_syncEmployee($request),
        };
    }

    // Private Methods
    // =========================================================================

    /* SYNCS */
    /**
     * @param RequestRecord $request
     * @return bool
     */
    private function _syncEmployee(RequestRecord $request): bool
    {
        $employer = Employer::findOne($request->employerId);
        $employee = Employee::findOne($request->employeeId);

        if($employer && $employee) {
            $staffologyEmployer = $employer->staffologyId;
            $staffologyEmployee = $employee->staffologyId;
            $endpoint = '/employers/'.$staffologyEmployer.'/employees/'.$staffologyEmployee;

            return $this->_sync($endpoint, $request, 'employee');
        }

        return false;
    }

    /**
     * @param string $endpoint
     * @param RequestRecord $request
     * @param string $type
     * @return bool
     * @throws \craft\errors\MissingComponentException
     */
    private function _sync(string $endpoint, RequestRecord $request, string $type): bool
    {
        $api = App::parseEnv(Staff::$plugin->getSettings()->apiKeyStaffology);
        $base_url = 'https://api.staffology.co.uk'.$endpoint;
        $credentials = base64_encode('staff:' . $api);
        $client = new \GuzzleHttp\Client([
            'headers' => [
                'Authorization' => 'Basic ' . $credentials,
            ],
        ]);

        $json = json_decode($request->request);
        $data = json_decode(json_encode($json), true);

        try {
            $response = $client->put(
                $base_url,
                [
                    'json' => $data,
                ]
            );

            $result = Json::decodeIfJson($response->getBody()->getContents(), true);

            match (true) {
                $type === 'employee' => $this->_saveEmployee($result, $request)
            };

            $request->error = '';

            return true;
        } catch (GuzzleException $e) {
            Craft::error($e->getMessage(), __METHOD__);
            Craft::$app->getSession()->setNotice(Craft::t('staff-management', 'There were validation errors when saving to Staffology'));

            $request->status = "pending";
            $request->error = json_decode($e->getResponse()->getBody()->getContents())->error;
            $request->save();

            return false;
//            return json_decode($e->getResponse()->getBody()->getContents())->error;
        }
    }

    /* SAVES */
    /**
     * @param array $employee
     * @param RequestRecord $request
     * @throws \Throwable
     */
    private function _saveEmployee(array $employee, RequestRecord $request): void
    {
        $employeeName = ($employee['personalDetails']['firstName'] ?? ''). ' ' .($employee['personalDetails']['lastName'] ?? '');
        $employer = Employer::findOne($request->employerId)->toArray();
        $employer['id'] = $employer['staffologyId'];

        Staff::$plugin->employees->saveEmployee($employee, $employeeName, $employer);
    }
}