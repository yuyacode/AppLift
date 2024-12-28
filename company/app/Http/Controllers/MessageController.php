<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $threads = $request->user()
                    ->messageThreads()
                    ->orderBy('updated_at', 'desc')
                    ->with(['messages' => function ($query) {
                        $query->where('is_sent', 1)
                            ->orderBy('sent_at', 'desc')
                            ->orderBy('id', 'desc')
                            ->limit(1);
                    }])
                    ->get();

        return view('message.index', compact('threads'));
    }

    public function get_access_token(Request $request)
    {
        $access_token = $request->user()->messageApiCredential()->select('access_token')->first()->access_token;

        if (!$access_token) {
            Log::error('Unable to fetch the access token from the data store');

            // ユーザーには責任のない通信であり、全てのエラーの原因はサーバー側にあるため、一律で500を返す
            return response()->json([
                'message' => 'failed to get access token',
            ], 500);
        }

        return response()->json([
            'access_token' => $access_token
        ]);
    }

    public function refresh_access_token(Request $request)
    {
        $credential = $request->user()->messageApiCredential()->select('refresh_token', 'client_id', 'client_secret')->first();
        $refresh_token = $credential->refresh_token;
        $client_id     = $credential->client_id;
        $client_secret = $credential->client_secret;

        if (!$refresh_token || !$client_id || !$client_secret) {
            switch (true) {
                case !$refresh_token:
                    Log::error('Unable to fetch the refresh token from the data store');
                case !$client_id:
                    Log::error('Unable to fetch the client_id from the data store');
                case !$client_secret:
                    Log::error('Unable to fetch the client_secret from the data store');
            }

            // ユーザーには責任のない通信であり、全てのエラーの原因はサーバー側にあるため、一律で500を返す
            return response()->json([
                'message' => 'failed to refresh access token',
            ], 500);
        }

        $url = config('api.message_api_base_url_backend').'/token';

        $headers = [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ];

        $data = [
            'refresh_token' => $refresh_token,
            'client_id'     => $client_id,
            'client_secret' => $client_secret,
        ];

        try {
            $response = Http::withHeaders($headers)->post($url, $data);

            if (!$response->successful()) {
                Log::error($response->status());

                $response_body = $response->json();
                if (!is_null($response_body)) {
                    Log::error($response_body['message'] ?? '');
                    Log::error($response_body['detail'] ?? '');
                }

                // ユーザーには責任のない通信であり、全てのエラーの原因はサーバー側にあるため、一律で500を返す
                return response()->json([
                    'message' => 'failed to refresh access token',
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'message' => 'failed to refresh access token',
            ], 500);
        }

        return response()->json('');
    }
}
