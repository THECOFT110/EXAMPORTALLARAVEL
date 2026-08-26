<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * Check if fee is expired
     */
    public function isExpired(): bool
    {
        return $this->status === 'EXPIRED' ||
               ($this->status === 'UNPAID' && $this->due_date && $this->due_date->isPast());
    }

    /**
     * Generate unique challan number
     */
    public static function generateChallanNumber(): string
    {
        do {
            $challan = 'SALU-'.now()->format('Ymd').'-'.strtoupper(substr(md5(uniqid()), 0, 6));
        } while (static::where('challan_number', $challan)->exists());

        return $challan;
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
