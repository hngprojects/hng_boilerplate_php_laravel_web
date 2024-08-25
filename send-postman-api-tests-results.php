<?php


$baseUrl = getenv('APP_URL');

if (!$baseUrl) {
    $baseUrl = 'http://127.0.0.1:8000'; // Default to localhost if APP_URL is not set
    echo "Warning: APP_URL environment variable is not set. Using default: $baseUrl\n";
}

function processJsonAndSend($baseUrl)
{
    $jsonPath = 'result.json';

    if (!file_exists($jsonPath)) {
        die("Error: result.json file not found at $jsonPath\n");
    }

    $jsonContent = file_get_contents($jsonPath);
    if ($jsonContent === false) {
        die("Error: Unable to read result.json file\n");
    }

    $data = json_decode($jsonContent, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Error: Invalid JSON in result.json - " . json_last_error_msg() . "\n");
    }

    $lastChecked = date('Y-m-d H:M:S', filemtime($jsonPath));

    $processedData = [];
    foreach ($data['run']['executions'] as $execution) {
        $apiGroup = $execution['item']['name'] ?? 'Unknown API';
        $responseTime = $execution['response']['responseTime'] ?? null;
        $status = determineStatus($execution);
        $details = getDetails($execution);

        $processedData[] = [
            'api_group' => $apiGroup,
            'status' => $status,
            'last_checked' => $lastChecked,
            'response_time' => $responseTime,
            'details' => $details,
        ];
    }

    // echo "Processed Data:\n";
    // print_r($processedData);

    $ch = curl_init($baseUrl . '/api/v1/api-status');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($processedData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        echo "cURL Error: " . curl_error($ch) . "\n";
    }

    curl_close($ch);

    echo "Status: $httpCode\n";
    echo "Response: $response\n";
}





function determineStatus($execution)
{
    $responseCode = $execution['response']['code'] ?? null;
    $responseTime = $execution['response']['responseTime'] ?? null;

    if ($responseCode === null || $responseCode >= 500) {
        return 'Down';
    } elseif ($responseTime > 400) {
        return 'Degraded';
    } else {
        return 'Operational';
    }
}

function getDetails($execution)
{
    $responseCode = $execution['response']['code'] ?? null;
    $responseTime = $execution['response']['responseTime'] ?? null;

    if ($responseCode === null || $responseCode >= 500) {
        return 'API not responding (HTTP ' . ($responseCode ?? 'Unknown') . ')';
    } elseif ($responseTime > 400) {
        return 'High response time detected';
    } else {
        return 'All tests passed';
    }
}

processJsonAndSend($baseUrl);
