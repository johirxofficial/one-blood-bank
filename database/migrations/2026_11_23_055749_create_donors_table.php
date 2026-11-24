<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('blood_group', 3);

            $table->foreignId('division_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('upazila_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('union_id')->nullable()->constrained()->nullOnDelete();
            $table->string('village', 120)->nullable();
            $table->text('address_details')->nullable();

            $table->date('date_of_birth')->nullable();
            $table->string('gender', 10)->nullable();
            $table->unsignedSmallInteger('weight_kg')->nullable();
            $table->date('last_donation_date')->nullable();
            $table->date('available_from')->nullable();
            $table->boolean('is_available')->default(true);

            $table->string('status', 20)->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('rejection_reason', 500)->nullable();

            $table->unsignedInteger('total_donations')->default(0);
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->boolean('is_public')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'blood_group', 'district_id']);
            $table->index(['status', 'is_available']);
            $table->index('last_donation_date');
            $table->index('rating_avg');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donors');
    }
};
