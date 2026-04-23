<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->when($request->q, fn($q, $term) => $q->search($term))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'gstin'              => 'nullable|string|max:20',
            'pan'                => 'nullable|string|max:12',
            'email'              => 'nullable|email|max:255',
            'phone'              => 'nullable|string|max:20',
            'billing_address'    => 'nullable|string',
            'billing_city'       => 'nullable|string|max:100',
            'billing_state'      => 'nullable|string|max:100',
            'billing_state_code' => 'nullable|string|max:5',
            'billing_pincode'    => 'nullable|string|max:10',
            'payment_terms'      => 'nullable|integer|min:0',
            'notes'              => 'nullable|string',
        ]);

        $customer = Customer::create($data);

        if ($request->wantsJson()) {
            return response()->json($customer);
        }

        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'gstin'              => 'nullable|string|max:20',
            'pan'                => 'nullable|string|max:12',
            'email'              => 'nullable|email|max:255',
            'phone'              => 'nullable|string|max:20',
            'billing_address'    => 'nullable|string',
            'billing_city'       => 'nullable|string|max:100',
            'billing_state'      => 'nullable|string|max:100',
            'billing_state_code' => 'nullable|string|max:5',
            'billing_pincode'    => 'nullable|string|max:10',
            'payment_terms'      => 'nullable|integer|min:0',
            'notes'              => 'nullable|string',
        ]);

        $customer->update($data);

        if ($request->wantsJson()) {
            return response()->json($customer);
        }

        return redirect()->route('customers.index')->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted.');
    }

    public function search(Request $request)
    {
        $customers = Customer::active()
            ->search($request->q ?? '')
            ->select('id', 'name', 'gstin', 'billing_city', 'billing_state', 'billing_state_code', 'billing_address', 'phone', 'email', 'payment_terms')
            ->limit(10)
            ->get();

        return response()->json($customers);
    }
}
