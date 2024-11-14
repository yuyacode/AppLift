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
                        $query->orderBy('created_at', 'desc')
                        ->limit(1);
                    }])
                    ->get();

        return view('message.index', compact('threads'));
    }
}
