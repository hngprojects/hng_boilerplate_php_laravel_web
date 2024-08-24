<?php

namespace App\Console\Commands;

use DateTime;
use DateTimeZone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class StoreApiStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:api-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get test result from result.json file and save to the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        // Get test result from json file
        $data = file_get_contents(base_path('result.json'));

        $data = json_decode($data, true);

        // Get the collections and executions arrays from the data
        $collections = $data['collection']['item'];
        $executions = $data['run']['executions'];

        // Loop through each collection
        foreach ($collections as $collectionIndex => $collection) {

            // Loop through each item within the current collection and match it with the corresponding execution
            foreach ($collection['item'] as $itemIndex => $item) {

                $execution = $executions[$itemIndex] ?? null;
                $status = null;
                $response_time = null;

                if ($execution && isset($execution['assertions']) && is_array($execution['assertions'])) {
                    // Extract the first assertion and response time if available
                    $assertions = $execution['assertions'];
                    $status = $assertions[0]['assertion'] ?? null;
                    $response_time = $execution['response']['responseTime'] ?? null;
                }

                // Get the current date and time
                $date = new DateTime("now", new DateTimeZone('Africa/Lagos'));
                $last_checked = $date->format('Y-m-d h:i A');


                $data = [
                    'api_group' => $item['name'],
                    'method' => $item['request']['method'],
                    'status' => $status,
                    'response_time' => $response_time,
                    'last_checked' => $last_checked,
                    'details' => $this->getDetails($execution)
                ];

                $url = config('app.url') . '/api/v1/api-status';

                $response = Http::post($url, $data);

                if ($response->failed()) {

                    return $this->info('Failed to send API status data: '. $response->body());
                }

            }
        }

        $this->info('API status data stored successfully: ' . $response->body());
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
