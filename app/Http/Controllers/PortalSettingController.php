<?php

namespace App\Http\Controllers;

use App\Models\PortalSetting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PortalSettingController extends Controller
{
  
    // ─────────────────────────────────────────────────────────────
    // Agar aapke app mein multi-tenancy hai to yahan se tenant_id
    // liya jaata hai. Simple single-tenant app ke liye default 1.
    // ─────────────────────────────────────────────────────────────
    private function getTenantId(): int
    {
        // Multi-tenant: return auth()->user()->tenant_id;
        // Ya session se:  return session('tenant_id', 1);
        return auth()->user()->tenant_id ?? 1;
    }

    // ─────────────────────────────────────────────────────────────
    // GET /settings
    // Settings form dikhao
    // ─────────────────────────────────────────────────────────────
    public function index(): View
    {
        $tenantId = $this->getTenantId();
        $settings = PortalSetting::firstOrCreateForTenant($tenantId);

        return view('settings', compact('settings'));
    }

    // ─────────────────────────────────────────────────────────────
    // PUT /settings
    // Form submit — teen fields save karo
    // ─────────────────────────────────────────────────────────────
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'portal_name' => ['required', 'string', 'max:255'],
            'logo'        => ['nullable', 'image', 'max:2048'],
            'logoo'       => ['nullable', 'image', 'max:2048'],
        ]);
 
        $tenantId = $this->getTenantId();
        $settings = PortalSetting::firstOrCreateForTenant($tenantId);
 
        // ── Primary logo ──
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            // Delete old stored file
            if (!empty($settings->logo_path) && !str_starts_with($settings->logo_path, 'http')) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $settings->logo_path = $request->file('logo')->store('logos', 'public');
        } else {
            // No new file — keep existing path, or fall back to old URL field
            if (empty($settings->logo_path)) {
                $settings->logo_path = $request->input('existing_logo') ?: ($settings->logo_url ?? null);
            }
        }
 
        // ── Secondary logo ──
        if ($request->hasFile('logoo') && $request->file('logoo')->isValid()) {
            if (!empty($settings->logoo_path) && !str_starts_with($settings->logoo_path, 'http')) {
                Storage::disk('public')->delete($settings->logoo_path);
            }
            $settings->logoo_path = $request->file('logoo')->store('logos', 'public');
        } else {
            if (empty($settings->logoo_path)) {
                $settings->logoo_path = $request->input('existing_logoo') ?: ($settings->logoo_url ?? null);
            }
        }
 
        // ── Portal name ──
        $settings->portal_name = $request->input('portal_name');
 
        $settings->save();
 
        return redirect()
            ->route('settings.index')
            ->with('success', 'Settings saved successfully!');
    }
}