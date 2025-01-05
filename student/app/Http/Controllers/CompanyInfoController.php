<?php

namespace App\Http\Controllers;

use App\Models\CompanyInfo;
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

    public function show(Request $request, CompanyInfo $company_info): View
    {
        $members = $company_info->companyUsers()
                    ->with('review')
                    ->get();

        return view('company_info.show', compact('company_info', 'members'));
    }

    public function search(Request $request)
    {
        $keyword = $request->query('keyword');
        
        if (!$keyword) {
            return response()->json([
                'message' => 'Keyword is required.',
            ], 400);
        }

        $company_infos = CompanyInfo::searchByName($keyword)->get();

        return response()->json($company_infos);
    }
}