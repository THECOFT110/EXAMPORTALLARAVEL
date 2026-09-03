<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Fee extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'enrollment_id',
        'challan_number',
        'amount',
        'status',
        'due_date',
        'paid_at',
        'payment_method',
        'transaction_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Get the enrollment that owns the fee.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Scope a query to only include paid fees.
     */
    public function scopePaid($query)
    {
        return $query->whereIn('status', ['PAID', 'VERIFIED']);
    }

    /**
     * Scope a query to only include unpaid fees.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status', 'UNPAID');
    }

    /**
     * Scope a query to only include fees pending verification.
     */
    public function scopePendingVerification($query)
    {
        return $query->where('status', 'PENDING_VERIFICATION');
    }

    /**
     * Scope a query to only include expired fees.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'EXPIRED')
            ->orWhere(function ($q) {
                $q->where('status', 'UNPAID')
                    ->where('due_date', '<', now());
            });
    }

    /**
     * Check if fee is paid
     */
    public function isPaid(): bool
    {
        return in_array($this->status, ['PAID', 'VERIFIED']);
    }

    /**
     * Check if fee is pending verification
     */
    public function isPendingVerification(): bool
    {
        return $this->status === 'PENDING_VERIFICATION';
    }

    /**
     * Check if fee is expired
     */
    public function isExpired(): bool
    {
        return $this->status === 'EXPIRED' ||
               ($this->status === 'UNPAID' && $this->due_date && $this->due_date->isPast());
    }

    /**
     * Generate unique challan number with high entropy (cryptographically secure)
     */
    public static function generateChallanNumber(): string
    {
        do {
            $random = strtoupper(bin2hex(random_bytes(6)));
            $challan = 'SALU-'.now()->format('Ymd').'-'.$random;
        } while (static::where('challan_number', $challan)->exists());

        return $challan;
    }

    /**
     * Mark fee as pending verification after student submission
     */
    public function markAsPendingVerification(?string $paymentMethod = null, ?string $transactionId = null): void
    {
        $this->update([
            'status' => 'PENDING_VERIFICATION',
            'paid_at' => now(),
            'payment_method' => $paymentMethod,
            'transaction_id' => $transactionId,
        ]);
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(?string $paymentMethod = null, ?string $transactionId = null): void
    {
        $this->update([
            'status' => 'PAID',
            'paid_at' => now(),
            'payment_method' => $paymentMethod,
            'transaction_id' => $transactionId,
        ]);
    }

    /**
     * Mark as verified
     */
    public function markAsVerified(): void
    {
        $this->update([
            'status' => 'VERIFIED',
        ]);
    }
}
