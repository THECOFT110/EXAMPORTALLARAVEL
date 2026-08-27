<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            $table->string('subject_code', 50);
            $table->string('subject_name', 200);
            $table->integer('marks')->default(0);
            $table->integer('total_marks')->default(100);
            $table->string('grade', 10)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->index('enrollment_id');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
