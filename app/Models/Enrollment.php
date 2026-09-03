<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\QueryException;

class Enrollment extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'academic_year_id',
        'college_id',
        'program',
        'session',
        'semester',
        'father_name',
        'surname',
        'so_do_wo',
        'dob',
        'gender',
        'address',
        'city',
        'contact_number',
        'postal_address',
        'passing_year',
        'division_obtained',
        'last_exam_details',
        'roll_number',
        'photo_url',
        'name_of_board',
        'board',
        'exam_from_salu',
        'exam_salu_seat_no',
        'exam_salu_year',
        'eligibility_cert_no',
        'nationality',
        'religion',
        'domicile_province',
        'domicile_district',
        'migration_province',
        'migration_district',
        'academic_records',
        'documents',
        'status',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'academic_records' => 'array',
            'documents' => 'array',
        ];
    }

    /**
     * Get the user that owns the enrollment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the academic year that owns the enrollment.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the college that owns the enrollment.
     */
    public function college(): BelongsTo
    {
        return $this->belongsTo(College::class);
    }

    /**
     * Get the fees for the enrollment.
     */
    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class);
    }

    /**
     * Get the seat for the enrollment.
     */
    public function seat(): HasOne
    {
        return $this->hasOne(Seat::class);
    }

    /**
     * Get the admit card for the enrollment.
     */
    public function admitCard(): HasOne
    {
        return $this->hasOne(AdmitCard::class);
    }

    /**
     * Get the results for the enrollment.
     */
    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Scope a query to only include pending enrollments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    /**
     * Scope a query to only include approved enrollments.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'APPROVED');
    }

    /**
     * Scope a query to only include rejected enrollments.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'REJECTED');
    }

    /**
     * Scope a query to only include draft enrollments.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'DRAFT');
    }

    /**
     * Check if enrollment has paid fee
     */
    public function hasFeePaid(): bool
    {
        return $this->fees()
            ->whereIn('status', ['PAID', 'VERIFIED'])
            ->exists();
    }

    /**
     * Check if enrollment has admit card
     */
    public function hasAdmitCard(): bool
    {
        return $this->admitCard()->exists();
    }

    /**
     * Check if enrollment has results
     */
    public function hasResults(): bool
    {
        return $this->results()
            ->whereNotNull('published_at')
            ->exists();
    }

    /**
     * Generate collision-resistant roll number
     */
    public function generateRollNumber(): string
    {
        $year = now()->format('y');
        $prefix = "SALU-{$year}-";

        $latest = static::where('roll_number', 'like', "{$prefix}%")
            ->orderByDesc('roll_number')
            ->lockForUpdate()
            ->value('roll_number');

        if ($latest) {
            $lastNumber = (int) substr($latest, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = static::where('status', 'APPROVED')->count() + 1;
        }

        return $prefix . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Approve this enrollment, assigning a collision-safe roll number.
     *
     * Concurrent approvals can compute the same next roll number; the unique
     * constraint catches that, and we regenerate and retry instead of failing
     * or writing a duplicate.
     */
    public function approveWithRollNumber(): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () {
            $this->status = 'APPROVED';
            $this->rejection_reason = null;

            $generated = false;
            if (empty($this->roll_number)) {
                $this->roll_number = $this->generateRollNumber();
                $generated = true;
            }

            for ($attempt = 0; $attempt < 10; $attempt++) {
                try {
                    $this->save();

                    return;
                } catch (QueryException $e) {
                    if (! $this->isUniqueViolation($e) || ! $generated || $attempt === 9) {
                        throw $e;
                    }

                    $this->roll_number = $this->generateRollNumber();
                }
            }
        });
    }

    private function isUniqueViolation(QueryException $e): bool
    {
        $sqlState = (string) ($e->errorInfo[0] ?? $e->getCode());

        return in_array($sqlState, ['23505', '23000'], true)
            || str_contains($e->getMessage(), 'duplicate key')
            || str_contains($e->getMessage(), 'Duplicate entry');
    }

    /**
     * Get total marks
     */
    public function getTotalMarks(): int
    {
        return $this->results->sum('total_marks');
    }

    /**
     * Get obtained marks
     */
    public function getObtainedMarks(): int
    {
        return $this->results->sum('marks');
    }

    /**
     * Get percentage
     */
    public function getPercentage(): float
    {
        $total = $this->getTotalMarks();
        if ($total === 0) {
            return 0;
        }

        return round(($this->getObtainedMarks() / $total) * 100, 2);
    }
}
