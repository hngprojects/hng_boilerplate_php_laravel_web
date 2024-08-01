<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreated;
use App\Http\Requests\CreateNotificationRequest;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CreateNotificationRequest $request)
    {
        // Query the users to send notifications to
        $users = User::all();

        $newNotification = DB::transaction(function () use ($request, $users) {
            // Create Notification
            $notification = Notification::create([
                'type' => 'push',
                'title' => 'notification',
                'message' => $request->input('message'),
            ]);

            // Loop through each user
            foreach ($users as $user) {
                $user->notifications()->attach($notification->id, [
                    'id' => (string) Str::uuid(),
                    'status' => 'unread'
                ]);

                // Dispatch notification
                // if ($notification->type === 'push') {
                //     // create new pusher event for internal notifications
                //     event(new NotificationCreated($notification));
                // }
            }
            return $notification;
        });

        // Return 201
        return response()->json([
            'status' => 'success',
            'message' => 'Notification created successfully',
            'status_code' => 201,
            'data' => [
                'id' => $newNotification->id,
                // 'user_id' => $newNotification->id, // Todo
                'message' => $newNotification->message,
                'created_at' => $newNotification->created_at,
            ]
        ], 201);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

    }

    public function getByUser(Request $request)
    {
        $user = Auth::user();

        // Get status from query parameters if provided
        $isRead = $request->query('is_read');

        // Retrieve notifications based on the is_read filter
        $user = User::with(['notifications' => function ($query) use ($isRead) {
            if ($isRead) {
                if ($isRead === 'false') {
                    return $query->wherePivot('status', '<>', 'read');
                }
                return $query->wherePivot('status', $isRead);
            }
        }])->find($user->id);

        // Count all notifications and unread notifications
        $totalNotificationsCount = $user->notifications()->count();
        $unreadNotificationsCount = $user->notifications()->where('status', 'unread')->count();

        // Map notifications into desired output
        $notifications = $user->notifications->map(function ($notification) {
            $isRead = $notification->pivot->status === 'read';
            return [
                'id' => $notification->id,
                'message' => $notification->message,
                'is_read' => $isRead,
                'created_at' => $notification->created_at,
            ];
        });

        //
        $status = $isRead === 'false' ? 'Unread ' : '';

        return response()->json([
            'status' => 'success',
            'message' => "{$status}Notifications retrieved successfully",
            'status_code' => Response::HTTP_OK,
            'data' => [
                'total_notification_count' => $totalNotificationsCount,
                'total_unread_notification_count' => $unreadNotificationsCount,
                'notifications' => $notifications
            ]
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $notificationId)
    {
        $request->validate([
            'is_read' => 'boolean|required'
        ]);
        try {
            $notification = UserNotification::query()->findOrFail($notificationId);
            $notification->status = $request->is_read ? 'read' : 'unread';
            $notification->save();

            return response()->json([
                'status' => 'success',
                'message' => 'notification cleared successfully',
                'status_code' => Response::HTTP_OK,
                'data' => $notification,
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'notification not found',
                'status_code' => Response::HTTP_NOT_FOUND,
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'something went wrong',
                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        try {

            UserNotification::query()->where([
                ['user_id', auth()->id()],
                ['status', 'unread']
            ])->update([
                'status' => 'read'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'notifications cleared successfully',
                'status_code' => Response::HTTP_OK,
                'data' => UserNotification::query()->where('user_id', auth()->id())->get(),
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'notifications not found',
                'status_code' => Response::HTTP_NOT_FOUND,
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'something went wrong',
                'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
