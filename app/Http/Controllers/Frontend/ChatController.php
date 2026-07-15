<?php

namespace App\Http\Controllers\Frontend;

use App\Events\ChatEvent;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class ChatController extends Controller
{
    function sendMessage(Request $request) {
        $request->validate([
            'message' => ['required', 'max:1000'],
            'receiver_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', 'admin'),
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
            Log::warning('Chat message saved, but realtime broadcast failed.', [
                'sender_id' => $senderId,
                'receiver_id' => $request->receiver_id,
                'error' => $exception->getMessage(),
            ]);
        }

        return response(['status' => 'success', 'msgId' => $request->msg_temp_id]);
    }

    function getConversation(string $senderId) : Response {
        abort_unless(User::where('id', $senderId)->where('role', 'admin')->exists(), 403);

        $receiverId = auth()->user()->id;

        Chat::where('sender_id', $senderId)->where('receiver_id', $receiverId)->where('seen', 0)->update(['seen' => 1]);

        $messages = Chat::whereIn('sender_id', [$senderId, $receiverId])
            ->whereIn('receiver_id', [$senderId, $receiverId])
            ->with(['sender'])
            ->orderBy('created_at', 'asc')
            ->get();
        return response($messages);
    }
}
