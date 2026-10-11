<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseHead extends Model
{
    //
     protected $fillable = ['tenant_id', 'name'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function expenseRecords()
    {
        return $this->hasMany(ExpenseRecord::class);
    }
}
