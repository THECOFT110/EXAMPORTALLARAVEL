<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colleges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('principal_name')->nullable();
            $table->enum('type', ['BOYS', 'GIRLS', 'COED'])->default('COED');
            $table->integer('boys_capacity')->default(0);
            $table->integer('girls_capacity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('is_active');
        });
        
        // Add foreign key to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('college_id')
                  ->references('id')
                  ->on('colleges')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['college_id']);
        });
        
        Schema::dropIfExists('colleges');
    }
};
