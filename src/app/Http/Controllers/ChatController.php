<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatRequest;
use App\Models\Chat;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Item $item)
    {
        $soldItem = $item->soldItem;
        if (!$soldItem) {
            abort(404);
        }

        // 権限チェック: 購入者または出品者のみ閲覧可能
        if (Auth::id() !== $soldItem->buyer_id && Auth::id() !== $item->seller_id) {
            abort(403);
        }

        $chats = $soldItem->chats()->with('sender.profile')->orderBy('created_at', 'asc')->get();

        // サイドバー用の他の取引を取得
        $user = Auth::user();
        $otherTransactions = \App\Models\SoldItem::where(function ($query) use ($user) {
            $query->where('buyer_id', $user->id)
                ->orWhereHas('item', function ($q) use ($user) {
                    $q->where('seller_id', $user->id);
                });
        })
            ->whereDoesntHave('ratings', function ($query) use ($user) {
                $query->where('rater_id', $user->id);
            })
            ->where('id', '!=', $soldItem->id) // 現在表示中の取引は除外
            ->with(['item', 'chats'])
            ->get()
            ->sortByDesc(function ($soldItem) use ($user) {
                $latestChat = $soldItem->chats->where('sender_id', '!=', $user->id)->sortByDesc('created_at')->first();
                return $latestChat ? $latestChat->created_at : $soldItem->created_at;
            });

        return view('chat.index', compact('item', 'chats', 'otherTransactions'));
    }

    public function store(ChatRequest $request, Item $item)
    {
        $soldItem = $item->soldItem;
        if (!$soldItem) {
            abort(404);
        }

        // 権限チェック
        if (Auth::id() !== $soldItem->buyer_id && Auth::id() !== $item->seller_id) {
            abort(403);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_images', 'public');
        }

        Chat::create([
            'sender_id' => Auth::id(),
            'sold_item_id' => $soldItem->id,
            'message' => $request->message,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('chat.index', $item->id);
    }

    public function destroy(Chat $chat)
    {
        if ($chat->sender_id !== Auth::id()) {
            abort(403);
        }

        $chat->delete();

        return back();
    }
}
