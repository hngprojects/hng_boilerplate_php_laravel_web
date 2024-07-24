<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;

class ExportUserController extends Controller
{
    public function export($format = 'json', Request $request)
    {
        $user = $request->user()->with(['profile', 'products', 'organisations',])
            ->where('role', 'user')->find($request->user()->id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
                'status-code' => 404
            ], 404);
        }

        if ($format == 'json') {
            return $this->exportAsJson($user);
        }

        if ($format == 'csv') {
            return Excel::download(new UsersExport($user), 'user_data.csv');
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid format',
            'status-code' => 400
        ], 400);
    }

    private function exportAsJson($user)
    {

        return response()->json([
            'status' => 'success',
            'message' => 'User data returns successfully',
            'status_code' => 200,
            'data' => $user
        ]);
    }
}
