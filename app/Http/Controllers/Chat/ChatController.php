<?php

namespace App\Http\Controllers\Chat;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\getChatRequest;
use App\Http\Requests\Chat\storeChatRequest;

class ChatController extends Controller {
    /**
     * Gets chats
     *
     * @param GetChatRequest $request
     * @return JsonResponse
     */
    public function index(GetChatRequest $request): JsonResponse {
        $data = $request->validated();

        $isPrivate = 1;
        if ($request->has('is_private')) {
            $isPrivate = (int)$data['is_private'];
        }

        $chats = Chat::where('is_private', $isPrivate)
            ->hasParticipant(auth()->user()->id)
            ->whereHas('messages')
            ->with('lastMessage.user', 'participants.user')
            ->latest('updated_at')
            ->get();
        return response()->json([$chats], 200);
    }


    /**
     * Stores a new chat
     *
     * @param StoreChatRequest $request
     * @return JsonResponse
     */
    public function store(StoreChatRequest $request): JsonResponse {
        $data = $this->prepareStoreData($request);
        if ($data['userId'] === $data['otherUserId']) {
            return response()->json(['You can not create a chat with your own'], 422);
        }
        $previousChat = $this->getPreviousChat($data['otherUserId']);

        if ($previousChat === null) {

            $chat = Chat::create($data['data']);
            $chat->participants()->createMany([
                [
                    'user_id' => $data['userId']
                ],
                [
                    'user_id' => $data['otherUserId']
                ]
            ]);
            $chat->refresh()->load('lastMessage.user', 'participants.user');
            return response()->json([$chat], 200);
        }
        return response()->json([$previousChat->load('lastMessage.user', 'participants.user')], 200);
    }

    /**
     * Check if user and other user has previous chat or not
     *
     * @param int $otherUserId
     * @return mixed
     */
    private function getPreviousChat(int $otherUserId): mixed {

        $userId = auth()->user()->id;
        return Chat::where('is_private', 1)
            ->whereHas('participants', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereHas('participants', function ($query) use ($otherUserId) {
                $query->where('user_id', $otherUserId);
            })
            ->first();
    }
    /**
     * Prepares data for store a chat
     *
     * @param StoreChatRequest $request
     * @return array
     */
    private function prepareStoreData(StoreChatRequest $request): array {
        $data = $request->validated();
        $otherUserId = (int)$data['user_id'];
        unset($data['user_id']);
        $data['created_by'] = auth()->user()->id;
        return [
            'otherUserId' => $otherUserId,
            'userId' => auth()->user()->id,
            'data' => $data,
        ];
    }


    /**
     * Gets a single chat
     *
     * @param Chat $chat
     * @return JsonResponse
     */
    public function show(Chat $chat): JsonResponse {
        $chat->load('lastMessage.user', 'participants.user');
        return response()->json([$chat], 200);
    }
}
