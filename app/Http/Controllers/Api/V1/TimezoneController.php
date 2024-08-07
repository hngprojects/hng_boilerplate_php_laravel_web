<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Timezone;

class TimezoneController extends Controller
{
    public function index()
{
    $timezones = Timezone::all();
    return response()->json([
        'status' => 'success',
        'message' => 'Timezones retrieved successfully',
        'data' => $timezones
    ]);
}

}
