<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AcademicYear extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the enrollment window for the academic year.
     */
    public function enrollmentWindow(): HasOne
    {
        return $this->hasOne(EnrollmentWindow::class);
    }

    /**
     * Get the enrollments for the academic year.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Scope a query to only include active academic years.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to get current academic year
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_active', true)->first();
    }

    /**
     * Check if enrollment window is open
     */
    public function isEnrollmentOpen(): bool
    {
        $window = $this->enrollmentWindow;

        if (! $window || ! $window->is_open) {
            return false;
        }

        $now = now();

        return $now->between($window->start_date, $window->end_date);
    }
}
