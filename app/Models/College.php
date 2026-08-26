<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class College extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'code',
        'address',
        'city',
        'province',
        'district',
        'phone',
        'email',
        'principal_name',
        'type',
        'boys_capacity',
        'girls_capacity',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'boys_capacity' => 'integer',
            'girls_capacity' => 'integer',
        ];
    }

    /**
     * Get the users for the college.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the enrollments for the college.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Scope a query to only include active colleges.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the total capacity of the college
     */
    public function getTotalCapacityAttribute(): int
    {
        return $this->boys_capacity + $this->girls_capacity;
    }

    /**
     * Get available boys capacity
     */
    public function getAvailableBoysCapacity(string $academicYearId): int
    {
        $allocated = $this->enrollments()
            ->where('academic_year_id', $academicYearId)
            ->where('gender', 'MALE')
            ->where('status', 'APPROVED')
            ->count();

        return max(0, $this->boys_capacity - $allocated);
    }

    /**
     * Get available girls capacity
     */
    public function getAvailableGirlsCapacity(string $academicYearId): int
    {
        $allocated = $this->enrollments()
            ->where('academic_year_id', $academicYearId)
            ->where('gender', 'FEMALE')
            ->where('status', 'APPROVED')
            ->count();

        return max(0, $this->girls_capacity - $allocated);
    }
}
