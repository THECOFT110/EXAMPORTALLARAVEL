<?php

namespace App\Services;

use App\Models\AdmitCard;
use App\Models\College;
use App\Models\Enrollment;
use App\Models\Fee;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    /**
     * Generate fee challan PDF
     */
    public function generateChallan(Fee $fee, Enrollment $enrollment, User $user): string
    {
        $pdf = Pdf::loadView('pdfs.challan', [
            'fee' => $fee,
            'enrollment' => $enrollment,
            'user' => $user,
            'generated_at' => now(),
        ]);

        return $pdf->output();
    }

    /**
     * Generate admit card PDF
     */
    public function generateAdmitCard(AdmitCard $admitCard, Enrollment $enrollment, User $user): string
    {
        $pdf = Pdf::loadView('pdfs.admit-card', [
            'admitCard' => $admitCard,
            'enrollment' => $enrollment,
            'user' => $user,
            'seat' => $admitCard->seat,
            'generated_at' => now(),
        ]);

        return $pdf->output();
    }

    /**
     * Generate result card PDF
     */
    public function generateResultCard(Enrollment $enrollment, array $results, User $user): string
    {
        $totalMarks = collect($results)->sum('total_marks');
        $obtainedMarks = collect($results)->sum('marks');
        $percentage = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 2) : 0;

        $pdf = Pdf::loadView('pdfs.result-card', [
            'enrollment' => $enrollment,
            'results' => $results,
            'user' => $user,
            'total_marks' => $totalMarks,
            'obtained_marks' => $obtainedMarks,
            'percentage' => $percentage,
            'generated_at' => now(),
        ]);

        return $pdf->output();
    }

    /**
     * Generate application form PDF
     */
    public function generateApplicationForm(Enrollment $enrollment, User $user): string
    {
        $pdf = Pdf::loadView('pdfs.application-form', [
            'enrollment' => $enrollment,
            'user' => $user,
            'generated_at' => now(),
        ]);

        return $pdf->output();
    }

    /**
     * Generate enrollment card PDF
     */
    public function generateEnrollmentCard(Enrollment $enrollment, User $user): string
    {
        $pdf = Pdf::loadView('pdfs.enrollment-card', [
            'enrollment' => $enrollment,
            'user' => $user,
            'college' => $enrollment->college,
            'generated_at' => now(),
        ]);

        return $pdf->output();
    }

    /**
     * Generate college seat list PDF
     */
    public function generateCollegeSeatListPdf(College $college, $academicYear, $gender, array $students): string
    {
        $pdf = Pdf::loadView('pdfs.college-seat-list', [
            'college' => $college,
            'academicYear' => $academicYear,
            'gender' => $gender,
            'students' => $students,
            'generated_at' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->output();
    }

    /**
     * Generate complete college list PDF
     */
    public function generateCollegeCompleteListPdf(College $college, $academicYear, array $students): string
    {
        $pdf = Pdf::loadView('pdfs.college-complete-list', [
            'college' => $college,
            'academicYear' => $academicYear,
            'students' => $students,
            'generated_at' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->output();
    }
}
