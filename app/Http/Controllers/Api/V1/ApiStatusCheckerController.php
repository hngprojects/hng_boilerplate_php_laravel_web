<?php

namespace App\Http\Controllers\Api\V1;

use DateTime;
use DateTimeZone;
use App\Models\ApiStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApiStatusDataRequest;


class ApiStatusCheckerController extends Controller
{

    public function index()
    {
        $api_statuses = ApiStatus::all();

        return response()->json([
            'status_code' => 200,
            'message' => 'Api status data returned successfully',
            'data' => $api_statuses
        ]);
    }

    public function store(StoreApiStatusDataRequest $request)
    {

        $request->validated();

        try {
            $date = new DateTime("now", new DateTimeZone('Africa/Lagos'));
            $last_checked = $date->format('Y-m-d h:i A');

            // Update the api status record
            ApiStatus::updateOrCreate(['api_group' => $request->api_group], [
                'api_group' => $request->api_group,
                'method' => $request->method,
                'status' => $request->status,
                'response_time' => $request->response_time,
                'last_checked' => $request->last_checked ?? $last_checked,
                'details' => $request->details,
            ]);

            return response()->json([
                'status_code' => 200,
                'message' => "Data stored successfully"
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 200,
                'message' => "Server error" . $e->getMessage()
            ], 500);
        }

    }

}
