<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryMaster extends Model
{
    protected $table = 'library_master';

    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'parent_id',
        'status',
    ];
    const TYPE_ROOM = 1;
    const TYPE_ROW = 2;
    const TYPE_SHELF = 3;

    public function parent()
    {
        return $this->belongsTo(LibraryMaster::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(LibraryMaster::class, 'parent_id');
    }

    public static function getParentOptions($tenantId, $currentId = null, $type = null)
    {
        $query = self::where('tenant_id', $tenantId)
            ->where('status', 1);

        if ($type == self::TYPE_ROW) {
            $query->where('type', self::TYPE_ROOM);
        } elseif ($type == self::TYPE_SHELF) {
            $query->where('type', self::TYPE_ROW)
                ->whereHas('parent');
        }

        $parents = $query->where('id', '!=', $currentId)
            ->with('parent')
            ->orderBy('parent_id')
            ->orderBy('name')
            ->get();

        return $parents->map(function ($parent) {
            if ($parent->type == self::TYPE_ROW && $parent->parent) {
                $label = "{$parent->name} ({$parent->parent->name})";
            } else {
                $label = $parent->name . ' (' . self::getTypeLabel($parent->type) . ')';
            }

            return [
                'id' => $parent->id,
                'name' => $label
            ];
        });
    }
    public static function getTypeLabel($type)
    {
        $labels = [
            self::TYPE_ROOM => 'Room',
            self::TYPE_ROW => 'Row',
            self::TYPE_SHELF => 'Shelf'
        ];
        return $labels[$type] ?? 'Unknown';
    }
}