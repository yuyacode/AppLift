<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        return view('message.index', [
            // 'messageThreads' => $messageThreads,
            // 'users' => $users,
            // 'additionalData' => $additionalData
        ]);
    }
}
