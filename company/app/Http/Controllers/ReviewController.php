<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewAnswer;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function edit(Request $request): View
    {
        $review = $request->user()->review()->with(['reviewAnswer.reviewItem'])->first();

        $data = old() ?: $review;

        return view('review.edit', compact('review', 'data'));
    }

    public function update(Request $request, Review $reviews): RedirectResponse
    {
        $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'status'           => ['required', 'in:1,0'],
            'answers'          => ['array'],
            'answers.*.score'  => ['nullable', 'integer', 'min:0', 'max:100'],
            'answers.*.answer' => ['nullable', 'string'],
        ]);        
        
        $post = $request->all();

        Gate::authorize('update', $reviews);

        try {
            DB::transaction(function () use ($reviews, $post) {
                $reviews->update([
                    'title' => $post['title'],
                    'status' => $post['status'],
                ]);

                foreach ($post['answers'] as $id => $data) :
                    $reviewAnswer = ReviewAnswer::findOrFail($id);

                    Gate::authorize('update', $reviewAnswer);

                    $reviewAnswer->update([
                        'score'  => $data['score'],
                        'answer' => $data['answer'],
                    ]);
                endforeach;
            });
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()->withErrors(['登録中にエラーが発生しました。もう一度お試しください。']);
        }

        return to_route('review.edit')->with('status', 'レビューを保存しました');
    }
}