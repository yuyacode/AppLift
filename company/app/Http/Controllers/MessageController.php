<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $threads = $request->user()
                    ->messageThreads()
                    ->orderBy('updated_at', 'desc')
                    ->with(['messages' => function ($query) {
                        $query->orderBy('created_at', 'desc')  // statusがsendの中で、send_dateのdesc（その中でid desc）で1件取る（同じ時刻に複数のメッセージを予約送信している場合への対策）
                        ->limit(1);
                    }])
                    ->get();

        return view('message.index', compact('threads'));
    }

    public function get_access_token(Request $request)
    {
        $access_token = $request->user()->messageApiCredential->access_token;

        if (!$access_token) {
            return response()->json(['error' => 'access token not found'], 404);
        }

        return response()->json(['access_token' => $access_token]);
    }
}
