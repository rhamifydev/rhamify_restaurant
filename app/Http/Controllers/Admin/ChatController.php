<?php

namespace App\Http\Controllers\Admin;

use App\Events\ChatEvent;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class ChatController extends Controller
{
    function index(): View
    {
        $userId = auth()->user()->id;

        $senders = Chat::select('sender_id')
            ->where('receiver_id', $userId)
            ->where('sender_id', '!=', $userId)
            ->selectRaw('MAX(created_at) as latest_message_sent')
            ->groupBy('sender_id')
            ->orderByDesc('latest_message_sent')
            ->get();

        return view('admin.chat.index', compact('senders'));
    }

    function getConversation(string $senderId) : Response {
        abort_unless(User::where('id', $senderId)->where('role', 'user')->exists(), 403);

        $receiverId = auth()->user()->id;

        Chat::where('sender_id', $senderId)->where('receiver_id', $receiverId)->where('seen', 0)->update(['seen' => 1]);

        $messages = Chat::whereIn('sender_id', [$senderId, $receiverId])
            ->whereIn('receiver_id', [$senderId, $receiverId])
            ->with(['sender'])
            ->orderBy('created_at', 'asc')
            ->get();
        return response($messages);
    }

    function sendMessage(Request $request) {
        $request->validate([
            'message' => ['required', 'max:1000'],
            'receiver_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', 'user'),
            ],
        ]);

        $chat = new Chat();
        $chat->sender_id = auth()->user()->id;
        $chat->receiver_id = $request->receiver_id;
        $chat->message = $request->message;
        $chat->save();

        $avatar = auth()->user()->avatar_url;
        $senderId = auth()->user()->id;

        try {
            broadcast(new ChatEvent($request->message, $avatar, $request->receiver_id, $senderId, auth()->user()->name))->toOthers();
        } catch (Throwable $exception) {
            Log::warning('Admin chat message saved, but realtime broadcast failed.', [
                'sender_id' => $senderId,
                'receiver_id' => $request->receiver_id,
                'error' => $exception->getMessage(),
            ]);
        }

        return response(['status' => 'success', 'msgId' => $request->msg_temp_id]);
    }
}
