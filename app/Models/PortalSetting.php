<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalSetting extends Model
{
    use HasFactory;

    protected $table = 'portal_settings';

    protected $fillable = [
        'tenant_id',
        'portal_name',
        'logo_url',
        'logoo_url',
        'logo_path',
        'logoo_path',
        'parent_portal_enabled',
        'student_portal_enabled',
    ];

    protected $casts = [
        'parent_portal_enabled'  => 'boolean',
        'student_portal_enabled' => 'boolean',
    ];

    public static function forTenant(int $tenantId): ?self
    {
        return static::where('tenant_id', $tenantId)->first();
    }

    public static function firstOrCreateForTenant(int $tenantId): self
    {
        return static::firstOrCreate(
            ['tenant_id' => $tenantId],
            [
                'portal_name'            => 'My Portal',
                'logo_url'               => null,
                'logoo_url'              => null,
                'logo_path'              => null,
                'logoo_path'             => null,
                'parent_portal_enabled'  => true,
                'student_portal_enabled' => true,
            ]
        );
    }
}