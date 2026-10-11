<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Holidays;
use Illuminate\Http\Request;

class HrHolidaysController extends Controller
{
    public function index($tenantKey)
    {
        $holidays = Holidays::where('tenant_id', auth()->user()->tenant_id)
            ->latest()
            ->get();

        $holidaysJson = $holidays->map(function ($h) use ($tenantKey) {
            return [
                'id'             => $h->id,
                'name'           => $h->name,
                'date'           => $h->date,
                'date_formatted' => \Carbon\Carbon::parse($h->date)->format('d M Y'),
                'description'    => $h->description ?? '—',
                'status'         => $h->status,
                'edit_url'       => null, // Read-only for now, or point to admin if allowed
                'delete_url'     => null,
            ];
        });

        return view('hr.holidays.index', compact('holidays', 'holidaysJson', 'tenantKey'));
    }
}
