<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Seat extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'enrollment_id',
        'exam_center',
        'room_no',
        'seat_no',
        'exam_date',
        'exam_time',
    ];

    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
            'exam_time' => 'datetime',
        ];
    }

    /**
     * Get the enrollment that owns the seat.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Get the admit card for the seat.
     */
    public function admitCard(): HasOne
    {
        return $this->hasOne(AdmitCard::class);
    }

    /**
     * Get full seat information
     */
    public function getFullSeatInfoAttribute(): string
    {
        return "{$this->exam_center} - Room: {$this->room_no}, Seat: {$this->seat_no}";
    }
}
