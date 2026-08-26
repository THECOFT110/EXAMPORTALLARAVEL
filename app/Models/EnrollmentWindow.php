<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnrollmentWindow extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'academic_year_id',
        'start_date',
        'end_date',
        'is_open',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'is_open' => 'boolean',
        ];
    }

    /**
     * Get the academic year that owns the enrollment window.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Scope a query to only include open windows.
     */
    public function scopeOpen($query)
    {
        return $query->where('is_open', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    /**
     * Check if the window is currently open
     */
    public function isCurrentlyOpen(): bool
    {
        if (! $this->is_open) {
            return false;
        }

        $now = now();

        return $now->between($this->start_date, $this->end_date);
    }
}
