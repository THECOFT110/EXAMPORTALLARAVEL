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
        'must_change_password',
        'password_changed_at',
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
            'must_change_password' => 'boolean',
            'password_changed_at' => 'datetime',
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
     * Scope query to find user by CNIC digits or formatted CNIC
     */
    public function scopeWhereCnicDigits($query, string $digits)
    {
        $cleanDigits = preg_replace('/\D/', '', $digits);
        if (strlen($cleanDigits) === 13) {
            $formatted = substr($cleanDigits, 0, 5).'-'.substr($cleanDigits, 5, 7).'-'.substr($cleanDigits, 12, 1);

            return $query->where(function ($q) use ($cleanDigits, $formatted) {
                $q->where('cnic', $formatted)
                    ->orWhere('cnic', $cleanDigits)
                    ->orWhereRaw("REPLACE(cnic, '-', '') = ?", [$cleanDigits]);
            });
        }

        return $query->where('cnic', $digits);
    }

    /**
     * Scope query to find user by phone digits or formatted phone
     */
    public function scopeWherePhoneDigits($query, string $digits)
    {
        $cleanDigits = preg_replace('/\D/', '', $digits);
        if (strlen($cleanDigits) === 11) {
            $formatted = substr($cleanDigits, 0, 4).'-'.substr($cleanDigits, 4);

            return $query->where(function ($q) use ($cleanDigits, $formatted) {
                $q->where('phone', $formatted)
                    ->orWhere('phone', $cleanDigits)
                    ->orWhereRaw("REPLACE(REPLACE(phone, '-', ''), ' ', '') = ?", [$cleanDigits]);
            });
        }

        return $query->where('phone', $digits);
    }

    /**
     * Scope query to find user by email or CNIC
     */
    public function scopeWhereEmailOrCnic($query, string $input)
    {
        $inputTrimmed = trim($input);
        $emailLower = strtolower($inputTrimmed);
        $cnicDigits = preg_replace('/\D/', '', $inputTrimmed);

        return $query->where(function ($q) use ($inputTrimmed, $emailLower, $cnicDigits) {
            $q->where('email', $emailLower)
                ->orWhere('cnic', $inputTrimmed);

            if (strlen($cnicDigits) === 13) {
                $formattedCnic = substr($cnicDigits, 0, 5).'-'.substr($cnicDigits, 5, 7).'-'.substr($cnicDigits, 12, 1);
                $q->orWhere('cnic', $formattedCnic)
                    ->orWhereRaw("REPLACE(cnic, '-', '') = ?", [$cnicDigits]);
            }
        });
    }

    /**
     * Normalize CNIC format and reject invalid CNICs
     */
    public function setCnicAttribute($value): void
    {
        if (empty($value)) {
            $this->attributes['cnic'] = $value;

            return;
        }

        $digits = preg_replace('/\D/', '', (string) $value);
        if (strlen($digits) !== 13) {
            throw new \InvalidArgumentException('CNIC must contain exactly 13 digits');
        }

        $this->attributes['cnic'] = substr($digits, 0, 5).'-'.
                                    substr($digits, 5, 7).'-'.
                                    substr($digits, 12, 1);
    }

    /**
     * Normalize phone format
     */
    public function setPhoneAttribute($value): void
    {
        if ($value) {
            $digits = preg_replace('/\D/', '', (string) $value);
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
