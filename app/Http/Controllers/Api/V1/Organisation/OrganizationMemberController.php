<?php

namespace App\Http\Controllers\Api\V1\Organisation;

use App\Http\Controllers\Controller;
use App\Models\Organisation;
use App\Models\OrganisationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

    public function searchMembers($org_id, Request $request)
    {
        // Validate the organization ID format
        $validator = Validator::make(['org_id' => $org_id], [
            'org_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid organization ID',
                'status_code' => 400,
            ], 400);
        }

        $user = Auth::user();

        // Fetch the organization
        $organization = Organisation::where('org_id', $org_id)->first();
        if (!$organization) {
            return response()->json([
                'message' => 'Organization does not exist',
                'status_code' => 404
            ], 404);
        }

        // Get the search term from the request
        $searchTerm = $request->input('search', '');

        // Query users belonging to the organization and matching the search term
        $users = $organization->users()
            ->where(function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
            })
            ->get();

        return response()->json([
            'message' => 'Users retrieved successfully',
            'status_code' => 200,
            'data' => $users
        ]);
    }
}
