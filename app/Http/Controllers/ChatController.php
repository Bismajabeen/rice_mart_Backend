<?php
namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Shop;
use App\Models\User;
use App\Models\AppNotification;
use App\Events\NewNotificationEvent;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // ── Get or Create Conversation ────────────────────────
    public function startConversation(Request $request)
    {
        $buyer = User::where('remember_token', $request->bearerToken())->first();
        if (!$buyer) return response()->json(['message' => 'Unauthorized.'], 401);

        $request->validate(['shop_id' => 'required|integer']);

        $shop = Shop::find($request->shop_id);
        if (!$shop) return response()->json(['message' => 'Shop not found.'], 404);

        $conversation = Conversation::firstOrCreate(
            ['buyer_id' => $buyer->id, 'shop_id' => $shop->id],
            ['seller_id' => $shop->seller_id, 'last_message_at' => now()]
        );

        return response()->json([
            'conversation' => $conversation->load(['shop', 'seller', 'lastMessage'])
        ], 200);
    }

    // ── Get All Conversations for User ────────────────────
    public function getConversations(Request $request)
    {
        $user = User::where('remember_token', $request->bearerToken())->first();
        if (!$user) return response()->json(['message' => 'Unauthorized.'], 401);

        $conversations = Conversation::with(['shop', 'buyer', 'seller', 'lastMessage'])
            ->where('buyer_id', $user->id)
            ->orWhere('seller_id', $user->id)
            ->orderByDesc('last_message_at')
            ->get();

        return response()->json(['conversations' => $conversations], 200);
    }

    // ── Get Messages in Conversation ──────────────────────
    public function getMessages(Request $request, $conversationId)
    {
        $user = User::where('remember_token', $request->bearerToken())->first();
        if (!$user) return response()->json(['message' => 'Unauthorized.'], 401);

        $conversation = Conversation::find($conversationId);
        if (!$conversation) return response()->json(['message' => 'Not found.'], 404);

        // Mark as read
        Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $user->id)
            ->update(['is_read' => true]);

        $messages = Message::where('conversation_id', $conversationId)
            ->with('sender:id,name')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['messages' => $messages], 200);
    }

    // ── Send Message ──────────────────────────────────────
    public function sendMessage(Request $request)
    {
        $user = User::where('remember_token', $request->bearerToken())->first();
        if (!$user) return response()->json(['message' => 'Unauthorized.'], 401);

        $request->validate([
            'conversation_id' => 'required|integer',
            'message'         => 'required|string|max:1000',
        ]);

        $conversation = Conversation::find($request->conversation_id);
        if (!$conversation) return response()->json(['message' => 'Not found.'], 404);

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'sender_id'       => $user->id,
            'message'         => $request->message,
        ]);

        $conversation->update(['last_message_at' => now()]);

        // ── Notification bhejna: dusre banda (receiver) ko batana ──
        $receiverId = $conversation->buyer_id == $user->id
            ? $conversation->seller_id
            : $conversation->buyer_id;

        $notification = AppNotification::create([
            'user_id' => $receiverId,
            'type' => 'chat_message',
            'title' => 'New Message',
            'message' => $user->name . ' sent you a message',
            'related_id' => $conversation->id,
        ]);

        broadcast(new NewNotificationEvent($notification));

        return response()->json([
            'message' => $message->load('sender:id,name')
        ], 201);
    }
}