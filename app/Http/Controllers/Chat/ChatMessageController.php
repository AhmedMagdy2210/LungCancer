<?php

namespace App\Http\Controllers\Chat;

use App\Models\Chat;
use App\Models\User;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use App\Events\NewMessageSent;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\GetMessageRequest;
use App\Http\Requests\Chat\StoreMessageRequest;


class ChatMessageController extends Controller {
    /**
     * Gets chat message
     *
     * @param GetMessageRequest $request
     * @return JsonResponse
     */
    public function index(GetMessageRequest $request): JsonResponse {
        $data = $request->validated();
        // dd($data);
        $chatId = $data['chat_id'];
        $currentPage = $data['page'];
        $pageSize = $data['page_size'] ?? 15;

        $messages = ChatMessage::where('chat_id', $chatId)
            ->with('user')
            ->latest('created_at')
            ->simplePaginate(
                $pageSize,
                ['*'],
                'page',
                $currentPage
            );

        return response()->json([$messages->getCollection()], 200);
    }

    /**
     * Create a chat message
     *
     * @param StoreMessageRequest $request
     * @return JsonResponse
     */
    public function store(StoreMessageRequest $request): JsonResponse {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;

        $chatMessage = ChatMessage::create($data);
        $chatMessage->load('user');

        /// TODO send broadcast event to pusher and send notification to onesignal services
        $this->sendNotificationToOther($chatMessage);

        return response()->json([$chatMessage, 'Message has been sent successfully.'], 200);
    }

    /**
     * Send notification to other users
     *
     * @param ChatMessage $chatMessage
     */
    private function sendNotificationToOther(ChatMessage $chatMessage): void {

        broadcast(new NewMessageSent($chatMessage))->toOthers();
        $user = auth()->user();
        $userId = $user->id;
        $chat = Chat::where('id', $chatMessage->chat_id)
            ->with(['participants' => function ($query) use ($userId) {
                $query->where('user_id', '!=', $userId);
            }])
            ->first();
        if (count($chat->participants) > 0) {
            $otherUserId = $chat->participants[0]->user_id;

            $otherUser = User::where('id', $otherUserId)->first();
            $otherUser->sendNewMessageNotification([
                'messageData' => [
                    'senderName' => $user->name,
                    'message' => $chatMessage->message,
                    'chatId' => $chatMessage->chat_id
                ]
            ]);
        }
    }
}
