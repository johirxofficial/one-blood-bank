<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('patient_name', 120);
            $table->string('blood_group', 3);
            $table->unsignedTinyInteger('units')->default(1);
            $table->string('hospital', 160);
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('upazila_id')->nullable()->constrained()->nullOnDelete();
            $table->string('contact_phone', 20);
            $table->dateTime('needed_at');
            $table->text('note')->nullable();
            $table->string('status', 20)->default('open');
            $table->timestamps();

            $table->index(['status', 'district_id', 'needed_at']);
            $table->index(['status', 'blood_group']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blood_requests');
    }
};
