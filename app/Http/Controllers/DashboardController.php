<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Item;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_invoiced'  => Invoice::sum('grand_total'),
            'total_paid'      => Invoice::where('status', 'paid')->sum('grand_total'),
            'total_outstanding' => Invoice::whereIn('status', ['sent', 'partial'])
                                    ->selectRaw('SUM(grand_total - amount_paid)')->value('SUM(grand_total - amount_paid)') ?? 0,
            'total_overdue'   => Invoice::where('status', 'sent')
                                    ->where('due_date', '<', now()->toDateString())
                                    ->sum('grand_total'),
            'invoice_count'   => Invoice::count(),
            'customer_count'  => Customer::count(),
            'draft_count'     => Invoice::where('status', 'draft')->count(),
        ];

        $recent = Invoice::with('customer')
            ->orderByDesc('invoice_date')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $monthly = Invoice::selectRaw("DATE_FORMAT(invoice_date, '%Y-%m') as month, SUM(grand_total) as total")
            ->where('invoice_date', '>=', now()->subMonths(6)->startOfMonth())
            ->whereNotIn('status', ['cancelled'])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('dashboard', compact('stats', 'recent', 'monthly'));
    }
}
