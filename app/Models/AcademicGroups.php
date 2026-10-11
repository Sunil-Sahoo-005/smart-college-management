<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicGroups extends Model
{
    use HasFactory;

    protected $table = 'academic_groups';

    protected $fillable = [
        'tenant_id',
        'name',
        'parent_id',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    // Parent Group
    public function parent()
    {
        return $this->belongsTo(AcademicGroups::class, 'parent_id');
    }

    // Child Groups
    public function children()
    {
        return $this->hasMany(AcademicGroups::class, 'parent_id');
    }

    // Scope for active groups
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // Scope for tenant
    public function scopeForTenant($query)
    {
        return $query->where('tenant_id', auth()->user()->tenant_id);
    }
    public static function getHierarchyList($tenantId)
    {
        return self::select('id', 'name', 'parent_id')
            ->where('tenant_id', $tenantId)
            ->where('status', 1)
            ->orderBy('name')
            ->get();
    }

    public static function buildIndentedOptions($groups, $parentId = null, $level = 0, $visited = [])
    {
        $options = [];

        foreach ($groups as $group) {
            if ((string) $group->parent_id === (string) $parentId) {
                if (in_array($group->id, $visited)) {
                    continue;
                }

                $prefix = str_repeat('--', $level);
                $label = $prefix ? $prefix . ' ' . $group->name : $group->name;

                $options[] = [
                    'id' => $group->id,
                    'name' => $label,
                ];

                $options = array_merge(
                    $options,
                    self::buildIndentedOptions($groups, $group->id, $level + 1, array_merge($visited, [$group->id]))
                );
            }
        }

        return $options;
    }
}
