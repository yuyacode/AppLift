<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function edit(Request $request): View
    {
        $review = $request->user()->review()->with(['reviewAnswer.reviewItem'])->first();

        $data = old() ?: $review;

        return view('review.edit', compact('review', 'data'));
    }
}
