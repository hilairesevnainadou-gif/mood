<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundingRequest;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $report = [
            'users' => User::count(),
            'transactions' => Transaction::count(),
            'funding_requests' => FundingRequest::count(),
            'support_open' => SupportTicket::where('status', 'open')->count(),
        ];

        return view('admin.reports.index', compact('report'));
    }

    public function generate()
    {
        $report = [
            'users' => User::count(),
            'transactions' => Transaction::count(),
            'funding_requests' => FundingRequest::count(),
            'support_open' => SupportTicket::where('status', 'open')->count(),
            'generated_at' => now(),
        ];

        return view('admin.reports.generate', compact('report'));
    }
}
