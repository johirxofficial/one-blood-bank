<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->string('type', 20)->default('banner');
            $table->string('placement', 30)->default('sidebar');
            $table->string('image_path')->nullable();
            $table->string('target_url')->nullable();
            $table->text('code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedBigInteger('impressions')->default(0);
            $table->unsignedBigInteger('clicks')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'placement', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
