<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $conversations = $user->conversations()
            ->whereNull('deleted_at')
            ->withCount(['messages' => function ($query) {
                $query->where('is_superseded', 0);
            }])
            ->orderBy('pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        $selectedConversation = null;
        $messages = [];

        if ($request->has('conversation_id')) {
            $selectedConversation = $user->conversations()
                ->find($request->conversation_id);

            if ($selectedConversation) {
                $query = $selectedConversation->messages();
                
                if (!$request->boolean('include_versions')) {
                    $query->where('is_superseded', 0);
                }

                $messages = $query->orderBy('created_at', 'asc')
                    ->get(['id', 'role', 'content', 'created_at', 'version', 'parent_message_id', 'is_superseded']);
            }
        }

        return Inertia::render('Chat/Index', [
            'conversations' => $conversations,
            'selectedConversation' => $selectedConversation,
            'messages' => $messages,
        ]);
    }

    public function show(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $conversation = $user->conversations()->findOrFail($id);

        $query = $conversation->messages();
        
        if (!$request->boolean('include_versions')) {
            $query->where('is_superseded', 0);
        }

        $messages = $query->orderBy('created_at', 'asc')
            ->get(['id', 'role', 'content', 'created_at', 'version', 'parent_message_id', 'is_superseded']);

        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $conversation = Conversation::create([
            'user_id' => $user->id,
            'title' => null, // Create with null title as requested
        ]);

        return response()->json([
            'conversation' => $conversation,
        ]);
    }

    public function send(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $request->validate([
            'message' => 'required|string',
            'conversation_id' => 'nullable|exists:conversations,id',
        ]);

        $conversation = null;
        if ($request->conversation_id) {
            $conversation = $user->conversations()->findOrFail($request->conversation_id);
        } else {
            $conversation = Conversation::create([
                'user_id' => $user->id,
                'title' => null,
            ]);
        }

        // Get the last non-superseded message to set as parent
        $lastMessage = $conversation->messages()
            ->where('is_superseded', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        $userMessage = Message::create([
            'conversation_id' => $conversation->id,
            'parent_message_id' => $lastMessage?->id,
            'role' => 'user',
            'content' => $request->message,
            'version' => 1,
        ]);

        // Auto-generate title if it's the first message
        if (!$conversation->title) {
            // We'll generate a basic title first, then refine it with AI if possible
            $conversation->update([
                'title' => Str::limit($request->message, 40)
            ]);
        } else {
            // Update timestamp for sorting
            $conversation->touch();
        }

        return $this->processAiResponse($conversation, $userMessage);
    }

    private function generateAiTitle(Conversation $conversation, $firstMessage)
    {
        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::post(env('N8N_WEBHOOK_URL'), [
                'message' => "Generate a short, concise 3-5 word title for a chat that starts with this message: \"" . $firstMessage . "\". Respond ONLY with the title.",
                'history' => [],
                'conversation_id' => $conversation->id,
                'system_prompt' => 'You are a helpful assistant that generates short, descriptive chat titles. Do not use quotes, do not use periods, just the title.',
            ]);

            $title = $response->json()['output'] ?? null;
            if ($title) {
                return trim($title, '"\' .');
            }
        } catch (\Exception $e) {
            Log::error('Title generation failed: ' . $e->getMessage());
        }
        return Str::limit($firstMessage, 40);
    }

    private function processAiResponse(Conversation $conversation, Message $parentMessage)
    {
        $history = Message::where('conversation_id', $conversation->id)
            ->where('is_superseded', 0)
            ->where('id', '!=', $parentMessage->id) // Exclude current message from history
            ->orderBy('created_at', 'asc')
            ->take(10)
            ->get(['role', 'content'])
            ->map(function ($msg) {
                return [
                    'role' => $msg->role,
                    'content' => $msg->content,
                ];
            })
            ->values();

        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::post(env('N8N_WEBHOOK_URL'), [
                'message' => $parentMessage->content,
                'history' => $history,
                'conversation_id' => $conversation->id,
                'system_prompt' => $conversation->system_prompt ?? 'You are a helpful AI assistant',
            ]);

            $reply = $response->json()['output'] ?? 'Sorry, I encountered an error. Please try again.';
        } catch (\Exception $e) {
            $reply = 'Sorry, I encountered an error. Please try again.';
        }

        $assistantMessage = Message::create([
            'conversation_id' => $conversation->id,
            'parent_message_id' => $parentMessage->id,
            'role' => 'assistant',
            'content' => $reply,
            'version' => $parentMessage->version,
        ]);

        // If this was the first message, try to generate a better title using AI
        $isFirstMessage = $conversation->messages()->where('is_superseded', 0)->count() <= 2;
        if ($isFirstMessage && $conversation->title === Str::limit($parentMessage->content, 40)) {
            $betterTitle = $this->generateAiTitle($conversation, $parentMessage->content);
            $conversation->update(['title' => $betterTitle]);
        }

        return response()->json([
            'reply' => $reply,
            'conversation' => [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'pinned' => $conversation->pinned,
                'updated_at' => $conversation->updated_at,
            ],
            'user_message' => $parentMessage,
            'assistant_message' => $assistantMessage,
        ]);
    }

    public function rename(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $request->validate(['title' => 'required|string|max:255']);
        $conversation = $user->conversations()->findOrFail($id);
        $conversation->update(['title' => $request->title]);

        return response()->json($conversation);
    }

    public function destroyConversation($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $conversation = $user->conversations()->findOrFail($id);
        $conversation->delete();

        return response()->json(['message' => 'Conversation deleted']);
    }

    public function pin($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $conversation = $user->conversations()->findOrFail($id);
        $conversation->update(['pinned' => !$conversation->pinned]);

        return response()->json($conversation);
    }

    public function editMessage(Request $request, $id)
    {
        $request->validate(['content' => 'required|string']);
        $message = Message::findOrFail($id);
        $conversation = $message->conversation;

        // Mark all messages after this one as superseded
        Message::where('conversation_id', $conversation->id)
            ->where('created_at', '>=', $message->created_at)
            ->update(['is_superseded' => 1]);

        // Create new user message version
        $newUserMessage = Message::create([
            'conversation_id' => $conversation->id,
            'parent_message_id' => $message->parent_message_id,
            'role' => 'user',
            'content' => $request->content,
            'version' => $message->version + 1,
        ]);

        $conversation->touch();

        return $this->processAiResponse($conversation, $newUserMessage);
    }

    public function regenerateMessage($id)
    {
        $message = Message::findOrFail($id);
        if ($message->role !== 'assistant') {
            return response()->json(['error' => 'Only assistant messages can be regenerated'], 400);
        }

        $conversation = $message->conversation;
        $parentMessage = Message::findOrFail($message->parent_message_id);

        // Mark this message as superseded
        $message->update(['is_superseded' => 1]);

        $conversation->touch();

        return $this->processAiResponse($conversation, $parentMessage);
    }

    public function destroyMessage(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $message = Message::whereHas('conversation', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->findOrFail($id);

        $message->delete();

        return response()->json(['message' => 'Message deleted']);
    }
}
