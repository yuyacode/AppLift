<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CompanyInfoController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('only-master');
        
        $company_info = $request->user()->company_info;

        return view('company_info.index', compact('company_info'));
    }
}
