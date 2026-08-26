<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable;

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'father_name',
        'cnic',
        'email',
        'phone',
        'password',
        'role',
        'verification_code',
        'password_reset_token_hash',
        'password_reset_token_expires_at',
        'is_verified',
        'college_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
        'password_reset_token_hash',
    ];

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
            'is_verified' => 'boolean',
            'password_reset_token_expires_at' => 'datetime',
        ];
    }

    /**
     * Get the college that owns the user.
     */
    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }

    /**
     * Get the enrollments for the user.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the audit logs for the user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['ADMIN', 'SUPERADMIN']);
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'SUPERADMIN';
    }

    /**
     * Check if user is student
     */
    public function isStudent(): bool
    {
        return $this->role === 'STUDENT';
    }

    /**
     * Check if user is college admin
     */
    public function isCollegeAdmin(): bool
    {
        return $this->role === 'COLLEGE_ADMIN';
    }

    /**
     * Normalize CNIC format
     */
    public function setCnicAttribute($value): void
    {
        $digits = preg_replace('/\D/', '', $value);
        if (strlen($digits) === 13) {
            $this->attributes['cnic'] = substr($digits, 0, 5).'-'.
                                        substr($digits, 5, 7).'-'.
                                        substr($digits, 12, 1);
        } else {
            $this->attributes['cnic'] = $value;
        }
    }

    /**
     * Normalize phone format
     */
    public function setPhoneAttribute($value): void
    {
        if ($value) {
            $digits = preg_replace('/\D/', '', $value);
            if (strlen($digits) === 11 && str_starts_with($digits, '03')) {
                $this->attributes['phone'] = substr($digits, 0, 4).'-'.substr($digits, 4);
            } else {
                $this->attributes['phone'] = $value;
            }
        } else {
            $this->attributes['phone'] = null;
        }
    }

    /**
     * Normalize email to lowercase
     */
    public function setEmailAttribute($value): void
    {
        $this->attributes['email'] = strtolower(trim($value));
    }
}
