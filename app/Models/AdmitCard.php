<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmitCard extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'enrollment_id',
        'seat_id',
        'exam_date',
        'exam_center',
        'is_issued',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
            'is_issued' => 'boolean',
            'issued_at' => 'datetime',
        ];
    }

    /**
     * Get the enrollment that owns the admit card.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Get the seat for the admit card.
     */
    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }

    /**
     * Scope a query to only include issued admit cards.
     */
    public function scopeIssued($query)
    {
        return $query->where('is_issued', true);
    }

    /**
     * Issue the admit card
     */
    public function issue(): void
    {
        $this->update([
            'is_issued' => true,
            'issued_at' => now(),
        ]);
    }
}
