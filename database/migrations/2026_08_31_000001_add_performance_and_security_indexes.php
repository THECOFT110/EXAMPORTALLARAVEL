<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to add composite performance and security indexes.
     */
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->index(['academic_year_id', 'status'], 'idx_enrollments_year_status');
            $table->index(['college_id', 'status'], 'idx_enrollments_college_status');
        });

        Schema::table('fees', function (Blueprint $table) {
            $table->index(['enrollment_id', 'status'], 'idx_fees_enrollment_status');
            $table->index(['status', 'due_date'], 'idx_fees_status_due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex('idx_enrollments_year_status');
            $table->dropIndex('idx_enrollments_college_status');
        });

        Schema::table('fees', function (Blueprint $table) {
            $table->dropIndex('idx_fees_enrollment_status');
            $table->dropIndex('idx_fees_status_due_date');
        });
    }
};
