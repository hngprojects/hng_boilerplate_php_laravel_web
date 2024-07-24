<?php

namespace App\Http\Controllers\Api\V1\Organisation;

use App\Http\Controllers\Controller;
use App\Models\OrganisationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizationMemberController extends Controller
{
    public function index($organizationId, Request $request)
    {
        $user = Auth::user();

        // Validate organization ID
        if (!preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/', $organizationId)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Invalid organization ID',
                'status_code' => 400,
            ], 400);
        }

        // Check if the user has permission to view members
        $organizationUser = OrganisationUser::where('org_id', $organizationId)
            ->where('user_id', $user->id)
            ->first();

        if (!$organizationUser) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Unauthorized access',
                'status_code' => 400,
            ], 400);
        }

        // Pagination logic
        $page = $request->query('page', 1);
        $pageSize = $request->query('page_size', 10);

        // Fetch members of the organization
        $members = OrganisationUser::where('org_id', $organizationId)
            ->join('users', 'organisation_user.user_id', '=', 'users.id')
            ->paginate($pageSize, ['users.id as userId', 'users.name as firstName', 'users.email', 'users.phone'], 'page', $page);

        return response()->json([
            'status' => 'success',
            'message' => 'Organization members fetched successfully',
            'data' => [
                'members' => $members->items(),
                'pagination' => [
                    'currentPage' => $members->currentPage(),
                    'pageSize' => $members->perPage(),
                    'totalPages' => $members->lastPage(),
                    'totalItems' => $members->total(),
                ],
            ],
            'status_code' => 200,
        ], 200);
    }
}
