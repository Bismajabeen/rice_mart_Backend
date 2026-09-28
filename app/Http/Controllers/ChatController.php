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
    public function index(Request $request)
    {
        $user = Auth::user();

        
        $isSeller = method_exists($user, 'hasRole') ? $user->hasRole('seller') : false;
        $shop = $isSeller ? Shop::where('user_id', $user->id)->first() : null;

        if ($isSeller && $shop) {
            $conversations = Conversation::where('shop_id', $shop->id)
                ->whereHas('messages') 
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
            $conversations = Conversation::where('buyer_id', $user->id)
                ->whereHas('messages')
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

    
    public function messages(Request $request, $conversationId)
    {
        $user = Auth::user();
        $conversation = Conversation::findOrFail($conversationId);

        
        $shop = Shop::find($conversation->shop_id);
        $isBuyer  = $conversation->buyer_id === $user->id;
        $isSeller = $shop && $shop->user_id === $user->id;

        if (!$isBuyer && !$isSeller) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

       
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

    
    public function send(Request $request, $conversationId)
    {
        $request->validate(['body' => 'required|string|max:2000']);

        $user = Auth::user();
        $conversation = Conversation::findOrFail($conversationId);

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

        $conversation->update(['last_message_at' => now()]);

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
