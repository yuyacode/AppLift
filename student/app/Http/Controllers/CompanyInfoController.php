<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyInfoController extends Controller
{
    public function index(Request $request): View
    {
        $recent_viewed_companies = $request->user()
                        ->companyInfoViewLogs()
                        ->with('companyInfo')
                        ->orderBy('id', 'desc')
                        ->get()
                        ->pluck('companyInfo');

        return view('company_info.index', compact('recent_viewed_companies'));
    }
}