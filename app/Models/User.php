<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'tenant_id',
        'role',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'avatar_url',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    // Helper: get user by ID
    public static function getUserById($id)
    {
        return self::find($id);
    }

    // Helper: get user types (roles)
    public static function getUserTypes()
    {
        return [
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'teacher' => 'Teacher',
            'hr' => 'HR',
            'student' => 'Student',
            'parent' => 'Parent',
            'accountant' => 'Accountant',
            'librarian' => 'Librarian',
        ];
    }

    // Relationship: tenant
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class, 'tenant_id');
    }

    // public function roles()
    // {
    //     return $this->belongsToMany(Role::class, 'user_has_roles', 'user_id', 'role_id');
    // }
public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_has_roles', 'user_id', 'role_id');
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()
            ->pluck('name')
            ->contains($roleName);
    }
    public function permissions()
    {
        return $this->roles()->with('permissions')->get()
            ->pluck('permissions')
            ->flatten()
            ->unique('id');
    }

    public function hasPermissionTo(string $permissionName): bool
    {
        return $this->permissions()
            ->pluck('name')
            ->contains($permissionName);
    }

    // public function hasRole(string $roleName): bool
    // {
    //     return $this->roles()
    //         ->pluck('name')
    //         ->contains($roleName);
    // }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
