<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\ApiStatus;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


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

}
