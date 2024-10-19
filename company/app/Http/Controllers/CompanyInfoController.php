<?php

namespace App\Http\Controllers;

use App\Models\CompanyInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CompanyInfoController extends Controller
{
    public function index(Request $request): View
    {        
        $company_info = $request->user()->company_info;

        return view('company_info.index', compact('company_info'));
    }

    public function edit(CompanyInfo $company_info): View
    {
        Gate::authorize('update', $company_info);

        $data = old() ?: $company_info;

        return view('company_info.edit', compact('company_info', 'data'));
    }
}
