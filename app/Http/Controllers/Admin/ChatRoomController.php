<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsWithDataTable;
use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Repositories\ChatRoomRepository;
use App\Services\ChatModerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Admin-panel chat monitoring: rooms, message history, moderation.
 */
class ChatRoomController extends Controller
{
    use RespondsWithDataTable;

    public function __construct(
        protected ChatRoomRepository $rooms,
        protected ChatModerationService $moderation,
    ) {
    }

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', ChatRoom::class);

        if ($request->ajax()) {
            $query = $this->rooms->filtered($request->user(), $request->only(['search', 'type', 'status']));

            return $this->dataTable($request, $query, [
                'id' => fn ($r) => $r->id,
                'name' => fn ($r) => e($r->name),
                'type' => fn ($r) => '<span class="badge text-bg-secondary-subtle text-uppercase">'.e($r->type).'</span>',
                'batch' => fn ($r) => e($r->batch?->name ?? '—'),
                'members_count' => fn ($r) => $r->members_count,
                'messages_count' => fn ($r) => $r->messages_count,
                'status' => fn ($r) => status_badge($r->is_active),
                'actions' => fn ($r) => view('admin.chat.partials.actions', ['room' => $r])->render(),
            ], ['id', 'name', 'type', null, 'members_count', 'messages_count', 'is_active']);
        }

        return view('admin.chat.index');
    }

    public function show(Request $request, ChatRoom $chatRoom): View
    {
        $this->authorize('view', $chatRoom);

        $chatRoom->load(['members', 'batch', 'creator']);

        $messages = $chatRoom->messages()
            ->with('user')
            ->when($request->input('flagged'), fn ($q) => $q->where('is_flagged', true))
            ->latest()
            ->paginate(50);

        return view('admin.chat.show', [
            'room' => $chatRoom,
            'messages' => $messages,
        ]);
    }

    public function toggleStatus(ChatRoom $chatRoom): JsonResponse
    {
        $this->authorize('update', $chatRoom);

        $room = $this->moderation->toggleRoomStatus($chatRoom);

        return response()->json([
            'message' => $room->is_active ? 'Chat room activated.' : 'Chat room deactivated.',
            'is_active' => $room->is_active,
        ]);
    }

    public function flagMessage(ChatRoom $chatRoom, Message $message): JsonResponse
    {
        $this->authorize('update', $chatRoom);

        abort_unless($message->chat_room_id === $chatRoom->id, 404);

        $message = $this->moderation->flagMessage($message);

        return response()->json([
            'message' => $message->is_flagged ? 'Message flagged.' : 'Message unflagged.',
            'is_flagged' => $message->is_flagged,
        ]);
    }

    public function removeMessage(ChatRoom $chatRoom, Message $message): JsonResponse
    {
        $this->authorize('delete', $chatRoom);

        abort_unless($message->chat_room_id === $chatRoom->id, 404);

        $this->moderation->removeMessage($message);

        return response()->json(['message' => 'Message removed by moderator.']);
    }
}
