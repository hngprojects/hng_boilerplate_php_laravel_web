<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\UserNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

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
    public function create()
    {
        //
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
