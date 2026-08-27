<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'enrollment_id',
        'subject_code',
        'subject_name',
        'marks',
        'total_marks',
        'grade',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'marks' => 'integer',
            'total_marks' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Get the enrollment that owns the result.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Scope a query to only include published results.
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    /**
     * Calculate percentage for this subject
     */
    public function getPercentageAttribute(): float
    {
        if ($this->total_marks === 0) {
            return 0;
        }

        return round(($this->marks / $this->total_marks) * 100, 2);
    }

    /**
     * Check if passed
     */
    public function isPassed(): bool
    {
        return $this->percentage >= 33; // Assuming 33% is passing
    }

    /**
     * Auto-calculate grade based on marks
     */
    public static function calculateGrade(int $marks, int $totalMarks): string
    {
        $percentage = ($totalMarks > 0) ? ($marks / $totalMarks) * 100 : 0;

        return match (true) {
            $percentage >= 80 => 'A+',
            $percentage >= 70 => 'A',
            $percentage >= 60 => 'B',
            $percentage >= 50 => 'C',
            $percentage >= 40 => 'D',
            $percentage >= 33 => 'E',
            default => 'F',
        };
    }

    /**
     * Publish the result
     */
    public function publish(): void
    {
        $this->update([
            'published_at' => now(),
        ]);
    }
}
