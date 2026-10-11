<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    protected $fillable = ['email', 'otp', 'expires_at', 'used'];

    protected $casts = [
        'expires_at' => 'datetime',
        'used'       => 'boolean',
    ];

    /**
     * OTP valid hai ya nahi — used nahi aur abhi expire nahi hua
     */
    public function isValid(): bool
    {
        return ! $this->used && $this->expires_at->isFuture();
    }
}