<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        // Get the authenticated user
        $user = auth()->user();
        
        // Get conversations (users the current user has exchanged messages with)
        $conversations = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->get()
            ->map(function ($message) use ($user) {
                // Determine the other user in the conversation
                return $message->sender_id === $user->id 
                    ? $message->receiver_id 
                    : $message->sender_id;
            })
            ->unique()
            ->map(function ($userId) {
                return \App\Models\User::find($userId);
            });
        
        return view('messages.index', compact('conversations'));
    }

    /**
     * Display a conversation with a specific user.
     */
    public function show(\App\Models\User $user)
    {
        $currentUser = auth()->user();
        
        // Get messages between the two users
        $messages = Message::where(function ($query) use ($currentUser, $user) {
            $query->where('sender_id', $currentUser->id)
                  ->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($currentUser, $user) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', $currentUser->id);
        })
        ->orderBy('created_at', 'asc')
        ->get();
        
        // Mark messages as read
        Message::where('sender_id', $user->id)
              ->where('receiver_id', $currentUser->id)
              ->where('is_read', false)
              ->update(['is_read' => true]);
        
        return view('messages.show', compact('user', 'messages'));
    }

    /**
     * Store a new message.
     */
    public function store(Request $request, \App\Models\User $user)
    {
        $request->validate([
            'message' => 'required|string',
        ]);
        
        $message = new Message();
        $message->sender_id = auth()->id();
        $message->receiver_id = $user->id;
        $message->message = $request->message;
        $message->is_read = false;
        $message->save();
        
        return redirect()->back()->with('success', 'ส่งข้อความเรียบร้อยแล้ว');
    }
}
