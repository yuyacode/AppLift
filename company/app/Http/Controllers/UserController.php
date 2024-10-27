<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewAnswer;
use App\Models\ReviewItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserController extends Controller
{
    public function create(): View
    {
        $data = old() ?: [];
        return view('user.create', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
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

        return to_route('company_info.index')->with('status_company-member', 'メンバーを追加しました');
    }
}
