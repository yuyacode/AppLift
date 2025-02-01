<?php

namespace App\Http\Controllers;

use App\Models\CompanyInfo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

use App\Models\MessageApiCredential;
use App\Models\MessageThread;

class CompanyInfoController extends Controller
{
    public function index(Request $request): View
    {
        $company_info = $request->user()->companyInfo;
        $members = $company_info->users;

        return view('company_info.index', compact('company_info', 'members'));
    }

    public function edit(CompanyInfo $company_info): View
    {
        Gate::authorize('update', $company_info);

        $data = old() ?: $company_info;

        return view('company_info.edit', compact('company_info', 'data'));
    }

    public function update(Request $request, CompanyInfo $company_info): RedirectResponse
    {
        Gate::authorize('update', $company_info);

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'address'  => ['required', 'string', 'max:255'],
            'homepage' => ['required', 'string'],
        ]);

        $company_info->update($data);

        return to_route('company_info.index')->with('status_company-basic-info', '基本情報を変更しました');
    }

    public function test_insert()
    {
        MessageApiCredential::create([
            'user_id'       => 1,
            'client_id'     => "hoge",
            'client_secret' => "hoge",
            'access_token'  => "hoge",
            'refresh_token' => "hoge",
            'expires_at'    => "2025-01-26 14:26:18",
        ]);

        MessageThread::create([
            'company_user_id' => 1,
            'student_user_id' => 1,
        ]);
    }
}
