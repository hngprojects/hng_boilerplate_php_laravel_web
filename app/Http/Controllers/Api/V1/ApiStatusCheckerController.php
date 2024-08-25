<?php

namespace App\Http\Controllers\Api\V1;

use DateTime;
use DateTimeZone;
use App\Models\ApiStatus;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApiStatusDataRequest;


class ApiStatusCheckerController extends Controller
{

    public function index() {
        try {
            $apiStatuses = ApiStatus::all();

            $groupedStatuses = [
                'authentication' => ['statuses' => [], 'overall_status' => ''],
                'help_center' => ['statuses' => [], 'overall_status' => ''],
                'users' => ['statuses' => [], 'overall_status' => ''],
                'jobs' => ['statuses' => [], 'overall_status' => ''],
                'products' => ['statuses' => [], 'overall_status' => ''],
                'billing' => ['statuses' => [], 'overall_status' => ''],
                'payments' => ['statuses' => [], 'overall_status' => ''],
                'languages' => ['statuses' => [], 'overall_status' => ''],
                'blog' => ['statuses' => [], 'overall_status' => ''],
                'faqs' => ['statuses' => [], 'overall_status' => ''],
                'inquiries' => ['statuses' => [], 'overall_status' => ''],
                'squeeze' => ['statuses' => [], 'overall_status' => ''],
                'cookies' => ['statuses' => [], 'overall_status' => ''],
                'email_templates' => ['statuses' => [], 'overall_status' => ''],
                'invitation' => ['statuses' => [], 'overall_status' => ''],
                'newsletter_subscription' => ['statuses' => [], 'overall_status' => ''],
                'notification' => ['statuses' => [], 'overall_status' => ''],
                'settings' => ['statuses' => [], 'overall_status' => ''],
                'other' => ['statuses' => [], 'overall_status' => '']
            ];

            foreach ($apiStatuses as $status) {
                $group = $this->determineGroup($status->api_group);
                $groupedStatuses[$group]['statuses'][] = $status;
            }

            foreach ($groupedStatuses as $group => $data) {
                $groupedStatuses[$group]['overall_status'] = $this->calculateGroupStatus($data['statuses']);
            }

            return response()->json([
                'Status' => 200,
                'Message' => 'API status retrieved successfully',
                'data' => $groupedStatuses
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'Status' => 500,
                'Message' => "API status cannot be fetched: " . $e->getMessage()
            ], 500);
        }
    }

    private function calculateGroupStatus($statuses) {
        if (empty($statuses)) {
            return 'Unknown';
        }

        $hasDown = false;
        $hasDegraded = false;

        foreach ($statuses as $status) {
            if ($status->status === 'Down') {
                $hasDown = true;
                break;
            } elseif ($status->status === 'Degraded') {
                $hasDegraded = true;
            }
        }

        if ($hasDown) {
            return 'Down';
        } elseif ($hasDegraded) {
            return 'Degraded';
        } else {
            return 'Operational';
        }
    }

    private function determineGroup($apiGroup) {
        $lowercaseGroup = strtolower($apiGroup);

        if (strpos($lowercaseGroup, 'auth') !== false || strpos($lowercaseGroup, 'login') !== false || strpos($lowercaseGroup, 'logout') !== false || strpos($lowercaseGroup, 'register') !== false || strpos($lowercaseGroup, 'password') !== false) {
            return 'authentication';
        } elseif (strpos($lowercaseGroup, 'help') !== false || strpos($lowercaseGroup, 'article') !== false) {
            return 'help_center';
        } elseif (strpos($lowercaseGroup, 'user') !== false) {
            return 'users';
        } elseif (strpos($lowercaseGroup, 'job') !== false) {
            return 'jobs';
        } elseif (strpos($lowercaseGroup, 'product') !== false || strpos($lowercaseGroup, 'categories') !== false) {
            return 'products';
        } elseif (strpos($lowercaseGroup, 'billing') !== false) {
            return 'billings';
        } elseif (strpos($lowercaseGroup, 'payment') !== false) {
            return 'payments';
        } elseif (strpos($lowercaseGroup, 'language') !== false) {
            return 'language';
        }elseif (strpos($lowercaseGroup, 'blog') !== false || strpos($lowercaseGroup, 'comments') !== false) {
            return 'blog';
        } elseif (strpos($lowercaseGroup, 'faq') !== false) {
            return 'faqs';
        }  elseif (strpos($lowercaseGroup, 'inquiries') !== false) {
            return 'inquiries';
        } elseif (strpos($lowercaseGroup, 'squeeze') !== false) {
            return 'squeeze_pages';
        } elseif (strpos($lowercaseGroup, 'cookies') !== false) {
            return 'cookies_pages';
        } elseif (strpos($lowercaseGroup, 'email template') !== false) {
            return 'email_template';
        } elseif (strpos($lowercaseGroup, 'invitation') !== false) {
            return 'invitation';
        } elseif (strpos($lowercaseGroup, 'newsletter') !== false) {
            return 'newsletter_subscription';
        } elseif (strpos($lowercaseGroup, 'notification') !== false) {
            return 'notification';
        } elseif (strpos($lowercaseGroup, 'setting') !== false) {
            return 'settings';
        } else {
            return 'other';
        }
    }



    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            foreach ($request->all() as $item) {
                if($item==null){
                    continue;
                }
                $lastChecked = Carbon::createFromTimestamp($item['last_checked'])->setTimezone('Africa/Lagos')->format('Y-m-d H:i:s');

                ApiStatus::updateOrCreate(
                    ['api_group' => $item['api_group']],
                    [
                        'id' => Str::uuid()->toString(),
                        'status' => $item['status'],
                        'last_checked' => $lastChecked,
                        'response_time' => $item['response_time'],
                        'details' => $item['details'],
                    ]
                );
            }

            DB::commit();
            return response()->json(['message' => 'API statuses updated successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating API statuses: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while updating API statuses: ' . $e->getMessage()], 500);
        }
    }

}
