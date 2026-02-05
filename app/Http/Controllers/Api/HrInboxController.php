<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HrContact;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HrInboxController extends Controller
{
    /**
     * Get inbox messages for HR/Admin
     */
    public function index()
    {
        $user = Auth::user();

        // Only HR/Admins can access inbox
        if (!in_array(strtolower($user->role), ['hr', 'admin', 'administrator'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get pending and recent messages
        $messages = HrContact::with(['user', 'employee'])
            ->where('status', 'pending')
            ->orWhere('status', 'in_progress')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'sender_name' => $message->employee 
                        ? $message->employee->first_name . ' ' . $message->employee->last_name
                        : ($message->user->name ?? 'Unknown'),
                    'subject' => $message->subject,
                    'status' => $message->status,
                    'status_label' => ucfirst(str_replace('_', ' ', $message->status)),
                    'category' => $message->category,
                    'category_label' => ucfirst($message->category),
                    'time_ago' => $message->created_at->diffForHumans(),
                    'created_at' => $message->created_at
                ];
            });

        // Count pending messages
        $pending_count = HrContact::where('status', 'pending')->count();

        return response()->json([
            'messages' => $messages,
            'pending_count' => $pending_count
        ]);
    }
}
