<?php

namespace App\Http\Controllers;

use App\Models\CompanyInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        DB::transaction(function () use ($request, $company_info) {
            $company_info_view_logs_query = $request->user()->companyInfoViewLogs();
        
            $company_info_view_logs_query->where('company_info_id', $company_info->id)->delete();
            
            $count = $company_info_view_logs_query->count();
        
            if ($count >= 10) {
                $oldestRecord = $company_info_view_logs_query->orderBy('id', 'asc')->first();
                if ($oldestRecord) {
                    $oldestRecord->delete();
                }
            }

            $company_info_view_logs_query->create([
                'user_id' => $request->user()->id,
                'company_info_id' => $company_info->id,
            ]);
        });

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