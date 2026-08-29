<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class ChatController extends Controller
{
    // =====================================================
    // GET /api/conversations
    // Returns all conversations for the authenticated user.
    // Works for both buyers (lists their chats) and sellers
    // (lists chats for their shop).
    // =====================================================
    public function index(Request $request)
    {
        $user = Auth::user();

        // IMPORTANT: don't decide buyer/seller purely by "does a shop
        // row exist for this user_id" — a leftover/rejected/deleted
        // shop row can wrongly flip a real customer into the seller
        // branch and silently return an empty list.
        // Use the user's actual role instead.
        $isSeller = method_exists($user, 'hasRole') ? $user->hasRole('seller') : false;
        $shop = $isSeller ? Shop::where('user_id', $user->id)->first() : null;

        if ($isSeller && $shop) {
            $conversations = Conversation::where('shop_id', $shop->id)
                ->with(['buyer', 'lastMessage'])
                ->orderByDesc('last_message_at')
                ->get()
                ->map(function ($conv) use ($user) {
                    return [
                        'id'             => $conv->id,
                        'shop_id'        => $conv->shop_id,
                        'other_name'     => $conv->buyer->name ?? 'Unknown',
                        'last_message'   => $conv->lastMessage->body ?? '',
                        'last_at'        => $conv->last_message_at,
                        'unread_count'   => $conv->messages()
                                              ->where('is_read', false)
                                              ->where('sender_id', '!=', $user->id)
                                              ->count(),
                    ];
                });
        } else {
            // Buyer — list all their conversations
            $conversations = Conversation::where('buyer_id', $user->id)
                ->with(['shop', 'lastMessage'])
                ->orderByDesc('last_message_at')
                ->get()
                ->map(function ($conv) use ($user) {
                    return [
                        'id'             => $conv->id,
                        'shop_id'        => $conv->shop_id,
                        'other_name'     => $conv->shop->shop_name ?? 'Unknown Shop',
                        'last_message'   => $conv->lastMessage->body ?? '',
                        'last_at'        => $conv->last_message_at,
                        'unread_count'   => $conv->messages()
                                              ->where('is_read', false)
                                              ->where('sender_id', '!=', $user->id)
                                              ->count(),
                    ];
                });
        }

        return response()->json($conversations);
    }

    // =====================================================
    // GET /api/conversations/{id}/messages
    // Returns all messages in a conversation.
    // Also marks messages from the other party as read.
    // =====================================================
    public function messages(Request $request, $conversationId)
    {
        $user = Auth::user();
        $conversation = Conversation::findOrFail($conversationId);

        // Security: only buyer or the shop's owner can read
        $shop = Shop::find($conversation->shop_id);
        $isBuyer  = $conversation->buyer_id === $user->id;
        $isSeller = $shop && $shop->user_id === $user->id;

        if (!$isBuyer && !$isSeller) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Mark messages from the other person as read
        Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $conversation->messages()
            ->with('sender:id,name')
            ->get()
            ->map(fn($m) => [
                'id'         => $m->id,
                'body'       => $m->body,
                'sender_id'  => $m->sender_id,
                'sender_name'=> $m->sender->name,
                'is_mine'    => $m->sender_id === $user->id,
                'created_at' => $m->created_at->toISOString(),
            ]);

        return response()->json($messages);
    }

    // =====================================================
    // POST /api/conversations/start
    // Buyer opens a chat with a shop (creates conversation
    // if it doesn't exist, idempotent).
    // Body: { shop_id }
    // =====================================================
    public function start(Request $request)
    {
        $request->validate(['shop_id' => 'required|exists:shops,id']);

        $user = Auth::user();

        $conversation = Conversation::firstOrCreate(
            ['buyer_id' => $user->id, 'shop_id' => $request->shop_id],
            ['last_message_at' => now()]
        );

        return response()->json([
            'conversation_id' => $conversation->id,
            'shop_id'         => $conversation->shop_id,
        ], 201);
    }

    // =====================================================
    // POST /api/conversations/{id}/messages
    // Send a message in a conversation.
    // Body: { body: "Hello" }
    // =====================================================
    public function send(Request $request, $conversationId)
    {
        $request->validate(['body' => 'required|string|max:2000']);

        $user = Auth::user();
        $conversation = Conversation::findOrFail($conversationId);

        // Security check
        $shop = Shop::find($conversation->shop_id);
        $isBuyer  = $conversation->buyer_id === $user->id;
        $isSeller = $shop && $shop->user_id === $user->id;

        if (!$isBuyer && !$isSeller) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message = Message::create([
            'conversation_id' => $conversationId,
            'sender_id'       => $user->id,
            'body'            => $request->body,
            'is_read'         => false,
        ]);

        // Update last_message_at on the conversation
        $conversation->update(['last_message_at' => now()]);

        // =========================
        // NOTIFY THE OTHER PARTY — new message
        // (buyer sent it -> notify the shop owner; seller sent it ->
        // notify the buyer)
        // =========================
        $recipient = $isBuyer
            ? ($shop ? $shop->user : null)
            : User::find($conversation->buyer_id);

        NotificationService::send(
            $recipient,
            'chat_message',
            'New message',
            $user->name . ' sent you a message',
            ['conversation_id' => $conversation->id]
        );

        return response()->json([
            'id'          => $message->id,
            'body'        => $message->body,
            'sender_id'   => $message->sender_id,
            'sender_name' => $user->name,
            'is_mine'     => true,
            'created_at'  => $message->created_at->toISOString(),
        ], 201);
    }
}
