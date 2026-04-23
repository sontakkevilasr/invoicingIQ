<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::query()->orderByDesc('invoice_date')->orderByDesc('id');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($query) use ($q) {
                $query->where('number', 'like', "%{$q}%")
                      ->orWhere('customer_name', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'overdue') {
                $query->where('status', 'sent')->where('due_date', '<', now()->toDateString());
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('from')) $query->where('invoice_date', '>=', $request->from);
        if ($request->filled('to'))   $query->where('invoice_date', '<=', $request->to);

        $invoices = $query->paginate(20)->withQueryString();

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $number   = Invoice::peekNumber();
        $settings = \App\Models\Setting::all_settings();
        $customers = Customer::active()->orderBy('name')->get();
        return view('invoices.form', compact('number', 'settings', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'number'       => 'required|unique:invoices,number',
            'invoice_date' => 'required|date',
            'rows'         => 'required|array|min:1',
            'rows.*.item_name' => 'required|string',
            'rows.*.qty'       => 'required|numeric|min:0.001',
            'rows.*.rate'      => 'required|numeric|min:0',
        ]);

        // Advance the auto-sequence only if the user kept the suggested number
        if ($request->number === Invoice::peekNumber()) {
            Invoice::nextNumber();
        }

        DB::transaction(function () use ($request) {
            $invoice = Invoice::create($this->invoiceData($request));
            $this->syncItems($invoice, $request->rows ?? []);
            $this->recalculate($invoice);
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        $settings  = \App\Models\Setting::all_settings();
        $customers = Customer::active()->orderBy('name')->get();
        return view('invoices.form', compact('invoice', 'settings', 'customers'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'number'       => "required|unique:invoices,number,{$invoice->id}",
            'invoice_date' => 'required|date',
            'rows'         => 'required|array|min:1',
            'rows.*.item_name' => 'required|string',
            'rows.*.qty'       => 'required|numeric|min:0.001',
            'rows.*.rate'      => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $invoice) {
            $invoice->update($this->invoiceData($request));
            $invoice->items()->delete();
            $this->syncItems($invoice, $request->rows ?? []);
            $this->recalculate($invoice);
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice updated.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate(['status' => 'required|in:draft,sent,paid,partial,cancelled']);
        $invoice->update(['status' => $request->status]);
        return back()->with('success', 'Status updated.');
    }

    public function recordPayment(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'mode'         => 'required|in:cash,upi,neft,rtgs,cheque,card,other',
            'reference'    => 'nullable|string|max:100',
            'notes'        => 'nullable|string',
        ]);

        Payment::create(['invoice_id' => $invoice->id] + $data);

        $paid = $invoice->payments()->sum('amount');
        $status = $paid >= $invoice->grand_total ? 'paid' : 'partial';
        $invoice->update(['amount_paid' => $paid, 'status' => $status]);

        return back()->with('success', 'Payment recorded.');
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load('items', 'payments');
        $settings = \App\Models\Setting::all_settings();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.pdf', compact('invoice', 'settings'))
            ->setPaper('a4', 'portrait');
        return $pdf->download("Invoice-{$invoice->number}.pdf");
    }

    // ─── Helpers ─────────────────────────────────────────────

    private function invoiceData(Request $request): array
    {
        return [
            'number'                  => $request->number,
            'status'                  => $request->status ?? 'draft',
            'invoice_date'            => $request->invoice_date,
            'due_date'                => $request->due_date,
            'customer_id'             => $request->customer_id,
            'customer_name'           => $request->customer_name,
            'customer_gstin'          => $request->customer_gstin,
            'customer_billing_address'=> $request->customer_billing_address,
            'customer_city'           => $request->customer_city,
            'customer_state'          => $request->customer_state,
            'customer_state_code'     => $request->customer_state_code,
            'place_of_supply'         => $request->place_of_supply,
            'place_of_supply_code'    => $request->place_of_supply_code,
            'is_intra_state'          => $request->boolean('is_intra_state'),
            'discount_type'           => $request->discount_type ?? 'percent',
            'discount_value'          => $request->discount_value ?? 0,
            'visible_columns'         => $request->visible_columns,
            'notes'                   => $request->notes,
            'terms'                   => $request->terms,
        ];
    }

    private function syncItems(Invoice $invoice, array $rows): void
    {
        foreach ($rows as $i => $row) {
            InvoiceItem::create([
                'invoice_id'       => $invoice->id,
                'item_id'          => $row['item_id'] ?? null,
                'sort_order'       => $i,
                'item_name'        => $row['item_name'],
                'hsn_sac'          => $row['hsn_sac'] ?? null,
                'description'      => $row['description'] ?? null,
                'unit'             => $row['unit'] ?? 'Nos',
                'qty'              => $row['qty'],
                'rate'             => $row['rate'],
                'discount_percent' => $row['discount_percent'] ?? 0,
                'gst_rate'         => $row['gst_rate'] ?? 18,
            ]);
        }
    }

    private function recalculate(Invoice $invoice): void
    {
        $invoice->load('items');
        $isIntra    = $invoice->is_intra_state;
        $subTotal   = 0;
        $totalCgst  = 0;
        $totalSgst  = 0;
        $totalIgst  = 0;

        foreach ($invoice->items as $item) {
            $taxable  = $item->rate * $item->qty * (1 - $item->discount_percent / 100);
            $discAmt  = $item->rate * $item->qty - $taxable;
            $tax      = $taxable * $item->gst_rate / 100;
            $cgst     = $isIntra ? $tax / 2 : 0;
            $sgst     = $isIntra ? $tax / 2 : 0;
            $igst     = $isIntra ? 0 : $tax;
            $total    = $taxable + $tax;

            $item->update([
                'discount_amount' => round($discAmt, 2),
                'taxable_amount'  => round($taxable, 2),
                'cgst_rate'       => $isIntra ? $item->gst_rate / 2 : 0,
                'sgst_rate'       => $isIntra ? $item->gst_rate / 2 : 0,
                'igst_rate'       => $isIntra ? 0 : $item->gst_rate,
                'cgst_amount'     => round($cgst, 2),
                'sgst_amount'     => round($sgst, 2),
                'igst_amount'     => round($igst, 2),
                'total_tax'       => round($tax, 2),
                'total_amount'    => round($total, 2),
            ]);

            $subTotal  += $taxable;
            $totalCgst += $cgst;
            $totalSgst += $sgst;
            $totalIgst += $igst;
        }

        // Invoice-level discount
        $discVal    = $invoice->discount_value ?? 0;
        $discType   = $invoice->discount_type ?? 'percent';
        $discAmt    = $discType === 'percent' ? $subTotal * $discVal / 100 : min($discVal, $subTotal);
        $ratio      = $subTotal > 0 ? ($subTotal - $discAmt) / $subTotal : 1;
        $netCgst    = $totalCgst * $ratio;
        $netSgst    = $totalSgst * $ratio;
        $netIgst    = $totalIgst * $ratio;
        $netTax     = $netCgst + $netSgst + $netIgst;
        $grandRaw   = ($subTotal - $discAmt) + $netTax;
        $grand      = round($grandRaw);
        $roundOff   = $grand - $grandRaw;

        $invoice->update([
            'discount_amount' => round($discAmt, 2),
            'sub_total'       => round($subTotal, 2),
            'total_cgst'      => round($netCgst, 2),
            'total_sgst'      => round($netSgst, 2),
            'total_igst'      => round($netIgst, 2),
            'total_tax'       => round($netTax, 2),
            'round_off'       => round($roundOff, 2),
            'grand_total'     => $grand,
        ]);
    }
}
