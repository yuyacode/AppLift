<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CompanyInfo;
use App\Models\Review;
use App\Models\ReviewAnswer;
use App\Models\ReviewItem;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $company_info = CompanyInfo::create([]);

        $user = User::create([
            'company_info_id' => $company_info->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_master' => 1
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

        $url = config('api.message_api_base_url').'/register';

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

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
