<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserHasRole extends Model
{
    protected $table = 'user_has_roles';
    protected $fillable = ['user_id', 'role_id','updated_at','created_at'];
}
