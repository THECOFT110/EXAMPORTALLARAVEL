<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('enrollment_id')->unique()->constrained('enrollments')->onDelete('cascade');
            $table->string('exam_center', 200);
            $table->string('room_no', 50);
            $table->string('seat_no', 50);
            $table->date('exam_date')->nullable();
            $table->time('exam_time')->nullable();
            $table->timestamps();
            
            $table->index('enrollment_id');
        });

        Schema::create('admit_cards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('enrollment_id')->unique()->constrained('enrollments')->onDelete('cascade');
            $table->foreignUuid('seat_id')->nullable()->constrained('seats')->onDelete('set null');
            $table->date('exam_date')->nullable();
            $table->string('exam_center', 200)->nullable();
            $table->boolean('is_issued')->default(false);
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
            
            $table->index('enrollment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admit_cards');
        Schema::dropIfExists('seats');
    }
};
