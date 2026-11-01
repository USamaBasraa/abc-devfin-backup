<?php

namespace App\Http\Controllers\Grader;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{ 
    public function index()
    {
        // dd("123");
        $notifications = DB::select('
            SELECT ID AS "id",
                   TITLE AS "title",
                   CATEGORY AS "category",
                   DESCRIPTION AS "description",
                   STATUS AS "status",
                   CREATED_AT AS "created_at",
                   UPDATED_AT AS "updated_at"
            FROM NOTIFICATIONS
            ORDER BY ID DESC
        ');

        return view('grader.teacher.addNotifications', compact('notifications'));
    }

public function store(Request $request)
{
    $data = $this->validateNotification($request);

    try {
        $inserted = DB::insert(
            'INSERT INTO NOTIFICATIONS (
                TITLE,
                CATEGORY,
                DESCRIPTION
            ) VALUES (
                :title,
                :category,
                :description
            )',
            [
                'title'       => $data['title'],
                'category'    => $data['category'],
                'description' => $data['description'],
            ]
        );

        if (!$inserted) {
            throw new \Exception('Insert operation failed.');
        }

        return redirect()
            ->route('addNotifications')
            ->with('success', 'Notification added successfully.');

    } catch (\Throwable $e) {
        report($e);

        return redirect()
            ->route('addNotifications')
            ->withInput()
            ->with('error', 'Notification could not be added.');
    }
}
public function markAllAsRead()
{
    try {
        $updated = DB::update('
            UPDATE NOTIFICATIONS
            SET IS_READ = 1,
                UPDATED_AT = SYSTIMESTAMP
            WHERE STATUS = 1
              AND IS_READ = 0
        ');

        return response()->json([
            'success' => true,
            'updated_records' => $updated,
            'message' => 'All notifications marked as read.'
        ]);

    } catch (\Throwable $e) {
        report($e);

        return response()->json([
            'success' => false,
            'message' => 'Notifications could not be marked as read.'
        ], 500);
    }
}
public function markAsRead($notification)
{
    $updated = DB::table('NOTIFICATIONS')
        ->where('ID', $notification)
        ->update([
            'IS_READ'    => 1,
            'UPDATED_AT' => DB::raw('SYSTIMESTAMP'),
        ]);

    if (!$updated) {
        $exists = DB::table('NOTIFICATIONS')
            ->where('ID', $notification)
            ->exists();

        if (!$exists) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found.',
            ], 404);
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Notification marked as read.',
    ]);
}
    public function update(Request $request, $id)
{
    $data = $this->validateNotification($request);

    try {
        $updated = DB::update(
            'UPDATE NOTIFICATIONS
                SET TITLE = :title,
                    CATEGORY = :category,
                    DESCRIPTION = :description,
                    UPDATED_AT = SYSTIMESTAMP
              WHERE ID = :id',
            [
                'title'       => $data['title'],
                'category'    => $data['category'],
                'description' => $data['description'],
                'id'          => $id,
            ]
        );

        if ($updated === 0) {
            throw new \Exception('Notification not found or no changes made.');
        }

        return redirect()
            ->route('addNotifications')
            ->with('success', 'Notification updated successfully.');

    } catch (\Throwable $e) {
        report($e);

        return redirect()
            ->route('addNotifications')
            ->withInput()
            ->with('error', 'Notification could not be updated.');
    }
}

    public function toggle($id)
    {
        $updated = DB::update('
            UPDATE NOTIFICATIONS
               SET STATUS = CASE WHEN STATUS = 1 THEN 0 ELSE 1 END,
                   UPDATED_AT = SYSTIMESTAMP
             WHERE ID = :id
        ', ['id' => $id]);

        abort_if($updated === 0, 404, 'Notification not found.');

        return redirect()->route('addNotifications')
            ->with('success', 'Notification status updated successfully.');
    }

    public function destroy($id)
    {
        $deleted = DB::delete(
            'DELETE FROM NOTIFICATIONS WHERE ID = :id',
            ['id' => $id]
        );

        abort_if($deleted === 0, 404, 'Notification not found.');

        return redirect()->route('addNotifications')
            ->with('success', 'Notification deleted successfully.');
    }

    private function validateNotification(Request $request)
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:Faculty,Student,Staff,Faculty & Staff'],
            'description' => ['required', 'string'],
        ]);
    }
}
