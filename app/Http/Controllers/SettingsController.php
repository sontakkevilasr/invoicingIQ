<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function uploadLogo(Request $request)
    {
        $request->validate(['company_logo' => 'required|image|mimes:jpeg,png,gif|max:2048']);

        $existing = Setting::get('company_logo');
        if ($existing) {
            Storage::disk('public')->delete($existing);
        }

        $ext  = $request->file('company_logo')->getClientOriginalExtension();
        $path = $request->file('company_logo')->storeAs('logos', 'company_logo.' . $ext, 'public');
        Setting::set('company_logo', $path);

        return redirect()->route('settings.index')->with('success', 'Logo uploaded successfully.');
    }

    public function removeLogo()
    {
        $path = Setting::get('company_logo');
        if ($path) {
            Storage::disk('public')->delete($path);
        }
        Setting::set('company_logo', '');
        return redirect()->route('settings.index')->with('success', 'Logo removed.');
    }

    public function logoImage()
    {
        $path = Setting::get('company_logo');
        if (!$path) abort(404);

        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) abort(404);

        $ext  = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $mime = match ($ext) { 'png' => 'image/png', 'gif' => 'image/gif', default => 'image/jpeg' };

        return response()->file($fullPath, ['Content-Type' => $mime, 'Cache-Control' => 'max-age=3600']);
    }

    public static function logoBase64(array $settings): ?string
    {
        $logoPath = $settings['company_logo'] ?? '';
        if (!$logoPath) return null;
        $fullPath = storage_path('app/public/' . $logoPath);
        if (!file_exists($fullPath)) return null;
        $ext  = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'png'   => 'image/png',
            'gif'   => 'image/gif',
            default => 'image/jpeg',
        };
        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($fullPath));
    }
}
