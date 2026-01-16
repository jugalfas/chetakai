<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $conversations = auth()->user()->conversations()
            ->withCount('messages')
            ->latest()
            ->get();

        $selectedConversation = null;
        $messages = [];

        if ($request->has('conversation_id')) {
            $selectedConversation = auth()->user()->conversations()
                ->find($request->conversation_id);

            if ($selectedConversation) {
                $messages = $selectedConversation->messages()
                    ->orderBy('created_at', 'asc')
                    ->get(['id', 'role', 'content', 'created_at']);
            }
        }

        return Inertia::render('Chat/Index', [
            'conversations' => $conversations,
            'selectedConversation' => $selectedConversation,
            'messages' => $messages,
        ]);
    }

    public function show($id)
    {
        $conversation = auth()->user()->conversations()->findOrFail($id);

        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get(['id', 'role', 'content', 'created_at']);

        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    public function store(Request $request)
    {
        $conversation = Conversation::create([
            'user_id' => auth()->id(),
            'title' => $request->title ?? 'New Chat',
        ]);

        return response()->json([
            'conversation' => $conversation,
        ]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        // Get or create conversation
        if ($request->conversation_id) {
            $conversation = auth()->user()->conversations()->findOrFail($request->conversation_id);
            if ($conversation->title === null || $conversation->title === 'New Chat') {
                $conversation->update([
                    'title' => $this->generateTitleFromMessage($request->message),
                ]);
            }
        } else {
            $conversation = Conversation::create([
                'user_id' => auth()->id(),
                'title' => $this->generateTitleFromMessage($request->message),
            ]);
        }

        // Save user message
        Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $request->message,
        ]);

        // Get conversation history
        $history = Message::where('conversation_id', $conversation->id)
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

        // Call AI API
        try {
            $response = Http::post(env('N8N_WEBHOOK_URL'), [
                'message' => $request->message,
                'history' => $history,
                'conversation_id' => $conversation->id,
                'system_prompt' => 'You are a helpful AI assistant',
            ]);

            Log::info('AI Response', $response->json());
            $reply = $response->json()['output'] ?? 'Sorry, I encountered an error. Please try again.';
        } catch (\Exception $e) {
            Log::error('AI API Error', ['error' => $e->getMessage()]);
            $reply = 'Sorry, I encountered an error. Please try again.';
        }

        // Save assistant message
        Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $reply,
        ]);

        return response()->json([
            'reply' => $reply,
            'conversation_id' => $conversation->id,
        ]);
    }

    private function generateTitleFromMessage($message)
    {
        // Generate a title from the first few words of the message
        $words = explode(' ', $message);
        $title = implode(' ', array_slice($words, 0, 5));
        return strlen($title) > 50 ? substr($title, 0, 47) . '...' : $title;
    }
}
