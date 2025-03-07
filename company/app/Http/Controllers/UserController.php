<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewAnswer;
use App\Models\ReviewItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserController extends Controller
{
    public function create(): View
    {
        $data = old() ?: [];
        return view('user.create', compact('data'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'company_info_id' => $request->user()->companyInfo->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_master' => 0
        ]);

        $review = Review::create([
            'company_user_id' => $user->id,
            'status' => 0,
        ]);

        $default_review_item_ids = ReviewItem::where('is_default', 1)->pluck('id');

        $review_answers = array();
        $now = now();
        foreach ($default_review_item_ids as $id) :
            array_push($review_answers, [
                'review_id' => $review->id,
                'review_item_id' => $id,
                'created_at' => $now
            ]);
        endforeach;

        ReviewAnswer::insert($review_answers);

        $url = config('api.message_api_base_url_backend').'/register';

        $headers = [
            'Authorization' => 'Bearer '.config('api.message_api_key'),
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ];

        $data = [
            'user_id'  => $user->id,
            'app_kind' => 'company',
        ];

        try {
            $response = Http::withHeaders($headers)->post($url, $data);
            $response_body = $response->json();

            if ($response->successful()) {
                Log::info($response->status());
                if (!is_null($response_body)) {
                    Log::info($response_body['message'] ?? '');
                    Log::info($response_body['detail'] ?? '');
                }
            } else {
                Log::error($response->status());
                if (!is_null($response_body)) {
                    Log::error($response_body['message'] ?? '');
                    Log::error($response_body['detail'] ?? '');
                }
                return redirect()->back()->withErrors(['登録中にエラーが発生しました。もう一度お試しください。']);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()->withErrors(['登録中にエラーが発生しました。もう一度お試しください。']);
        }

        return to_route('company_info.index')->with('status_company-member', 'メンバーを追加しました');
    }
}
