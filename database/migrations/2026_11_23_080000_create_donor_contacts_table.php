<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donor_contacts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('donor_id')->constrained()->cascadeOnDelete();
            $table->string('label', 40)->default('Other');
            $table->string('phone', 30);
            $table->timestamps();

            $table->index(['donor_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donor_contacts');
    }
};
