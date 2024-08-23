<?php

namespace App\Http\Controllers\Api\V1;

use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class ApiStatusCheckerController extends Controller
{

    public function status()
    {
        // Load and decode the JSON file
        // $data = file_get_contents(__DIR__ . '/../result.json');
        $data = file_get_contents(base_path('/result.json'));


        // $data = file_get_contents('https://staging.api-php.boilerplate.hng.tech/storage/result.json');
        $data = json_decode($data, true);

        // Get the collections and executions arrays from the data
        $collections = $data['collection']['item'];
        $executions = $data['run']['executions'];

        // Initialize an array to hold the results
        $responseArray = [];

        // Loop through each collection
        foreach ($collections as $collectionIndex => $collection) {

            // Loop through each item within the current collection
            foreach ($collection['item'] as $itemIndex => $item) {
                // Initialize an array to store status, response_time, and assertions_count for each execution
                // $executionData = [
                //     'status' => null,
                //     'response_time' => null,
                // ];

                $status = null;
                $response_time = null;

                // Find matching execution for the current item (based on index or some other identifier)
                if (isset($executions[$itemIndex])) {
                    $execution = $executions[$itemIndex];

                    // Check if assertions key exists
                    if (isset($execution['assertions']) && is_array($execution['assertions'])) {
                        $assertions = $execution['assertions'];


                        // Loop through each assertion and extract relevant data
                        foreach ($assertions as $index => $assertion) {
                            if ($index === 0) {
                                // First assertion will be assigned to 'status'
                                // $executionData['status'] = $assertion['assertion'];
                                $status = $assertion['assertion'];
                            } elseif ($index === 1) {
                                // Second assertion will be assigned to 'response_time'
                                $response_time = $execution['response']['responseTime'];
                            }
                        }
                    }
                }

                $date = new DateTime("now", new DateTimeZone('Africa/Lagos'));
                $formattedDate = $date->format('Y-m-d h:i A');


                // Append only the name and execution_data to the response array
                $responseArray[] = [
                    'api_group' => $item['name'],
                    'method' => $item['request']['method'],
                    // 'execution_data' => $executionData,
                    'status' => $status,
                    'last_checked' => $formattedDate,
                    'details' => $this->getDetails($execution)
                ];
            }


        }

        // Return the accumulated results as a JSON response
        return response()->json([
            'data' => $responseArray
        ]);
    }


    private function getDetails($execution)
    {
        $response_code = $execution['response']['code'] ?? null;
        $response_time = $execution['response']['responseTime'] ?? null;

        if ($response_code >= 500 || $response_code === null) {
            return 'API not responding (HTTP ' . ($response_code ?? 'Unknown') . ')';
        } elseif ($response_time > 400) {
            return 'High response time detected';
        } else {
            return 'All tests passed';
        }
    }
}
