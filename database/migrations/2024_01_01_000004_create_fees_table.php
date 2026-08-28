<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            $table->string('challan_number', 50)->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['UNPAID', 'PENDING_VERIFICATION', 'PAID', 'VERIFIED', 'EXPIRED'])->default('UNPAID');
            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('transaction_id', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('challan_number');
            $table->index('enrollment_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
