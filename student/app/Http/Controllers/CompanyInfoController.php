<?php

namespace App\Http\Controllers;

use App\Models\CompanyInfo;
use App\Models\CompanyUser;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

use App\Models\CompanyInfoViewLog;
use App\Models\MessageThread;

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

    public function member(Request $request, CompanyInfo $company_info, CompanyUser $company_user): View
    {
        $review = Review::with([
            'reviewAnswers' => function ($query) {
                $query->select('review_id', 'review_item_id', 'score', 'answer');
            },
            'reviewAnswers.reviewItem' => function ($query) {
                $query->select('id', 'name');
            }
        ])
        ->where('company_user_id', $company_user->id)
        ->select('id', 'title', 'status')
        ->first();

        $company_info = $company_info->only([
                            'id',
                            'name',
                        ]);

        $company_user = $company_user->only([
                            'id',
                            'name',
                            'department',
                            'occupation',
                            'position',
                            'join_date',
                            'introduction'
                        ]);
                    
        return view('company_info.member', compact('company_info', 'company_user', 'review'));
    }

    public function search(Request $request): JsonResponse
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

    public function test_insert()
    {
        CompanyInfoViewLog::create([
            'user_id'         => 1,
            'company_info_id' => 1,
        ]);

        MessageThread::create([
            'company_user_id' => 1,
            'student_user_id' => 1,
        ]);
    }
}