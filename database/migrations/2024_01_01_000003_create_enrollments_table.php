<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignUuid('college_id')->nullable()->constrained('colleges')->onDelete('set null');
            
            // Program Details
            $table->string('program', 100);
            $table->string('session', 50);
            $table->string('semester', 20);
            
            // Personal Details
            $table->string('father_name');
            $table->string('surname', 100)->nullable();
            $table->string('so_do_wo', 50)->nullable(); // S/o, D/o, W/o
            $table->date('dob');
            $table->enum('gender', ['MALE', 'FEMALE', 'OTHER']);
            $table->text('address');
            $table->string('city', 100)->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->text('postal_address')->nullable();
            
            // Academic Details
            $table->string('passing_year', 20)->nullable();
            $table->string('division_obtained', 50)->nullable();
            $table->text('last_exam_details')->nullable();
            $table->string('roll_number', 50)->nullable()->unique();
            $table->string('photo_url', 500)->nullable();
            
            // Board & Exam
            $table->string('name_of_board', 100)->nullable();
            $table->string('board', 100)->nullable();
            $table->string('exam_from_salu', 10)->nullable();
            $table->string('exam_salu_seat_no', 50)->nullable();
            $table->string('exam_salu_year', 20)->nullable();
            $table->string('eligibility_cert_no', 100)->nullable();
            
            // Nationality & Domicile
            $table->string('nationality', 50)->default('Pakistani');
            $table->string('religion', 50)->nullable();
            $table->string('domicile_province', 50)->nullable();
            $table->string('domicile_district', 50)->nullable();
            $table->string('migration_province', 50)->nullable();
            $table->string('migration_district', 50)->nullable();
            
            // Academic Records & Documents (JSON)
            $table->json('academic_records')->nullable();
            $table->json('documents')->nullable();
            
            // Status
            $table->enum('status', ['DRAFT', 'PENDING', 'APPROVED', 'REJECTED'])->default('DRAFT');
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
            
            $table->index(['user_id', 'academic_year_id']);
            $table->index('status');
            $table->index('program');
            $table->index('college_id');
            $table->index('roll_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
