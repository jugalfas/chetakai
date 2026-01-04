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
    public function index()
    {
        return Inertia::render('Chat/Index', [
            'conversations' => auth()->user()->conversations()->latest()->get()
        ]);
    }

    public function send(Request $request)
    {
        $conversation = Conversation::firstOrCreate([
            'id' => $request->conversation_id,
            'user_id' => auth()->id(),
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $request->message,
        ]);

        $response = Http::post(env('N8N_WEBHOOK_URL'), [
            'message' => $request->message,
            'history' => Message::where('conversation_id', $conversation->id)
                ->latest()
                ->take(10)
                ->get(['role', 'content'])
                ->reverse()
                ->values(),
            'system_prompt' => 'You are a helpful AI assistant',
        ]);

        Log::info($response->json());
        $reply = $response->json()['reply'];

        Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $reply,
        ]);

        return response()->json(['reply' => $reply]);
    }
}
