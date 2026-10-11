<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMaster extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'strength', 'status'];

    /* ── Relationships ──────────────────────────────────────── */

    public function practicalGroups()
    {
        return $this->hasMany(PracticalGroups::class);
    }

    /* ── Accessors ──────────────────────────────────────────── */

    public function getDateFormattedAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('d M Y') : '';
    }

    public function getEditUrlAttribute(): string
    {
        return route('group_master.edit', $this);
    }

    public function getDeleteUrlAttribute(): string
    {
        return route('group_master.destroy', $this);
    }
}