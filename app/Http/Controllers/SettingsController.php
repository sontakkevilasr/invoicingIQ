<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all_settings();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name'    => 'required|string|max:255',
            'company_gstin'   => 'nullable|string|max:20',
            'company_pan'     => 'nullable|string|max:12',
            'company_address' => 'nullable|string',
            'company_city'    => 'nullable|string|max:100',
            'company_state'   => 'nullable|string|max:100',
            'company_state_code' => 'nullable|string|max:5',
            'company_phone'   => 'nullable|string|max:20',
            'company_email'   => 'nullable|email',
            'bank_name'       => 'nullable|string|max:100',
            'bank_acc_no'     => 'nullable|string|max:30',
            'bank_ifsc'       => 'nullable|string|max:15',
            'bank_branch'     => 'nullable|string|max:100',
            'invoice_prefix'  => 'required|string|max:10',
            'invoice_seq'     => 'required|integer|min:1',
            'default_terms'   => 'nullable|integer|min:0',
            'default_notes'   => 'nullable|string',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('settings.index')->with('success', 'Settings saved successfully.');
    }
}
