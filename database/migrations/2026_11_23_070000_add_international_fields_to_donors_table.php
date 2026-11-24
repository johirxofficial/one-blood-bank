<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donors', function (Blueprint $table) {
            $table->string('country', 80)->default('Bangladesh')->after('blood_group');
            $table->string('state', 120)->nullable()->after('country');
            $table->string('city', 120)->nullable()->after('state');
            $table->index('country');
        });
    }

    public function down(): void
    {
        Schema::table('donors', function (Blueprint $table) {
            $table->dropIndex(['country']);
            $table->dropColumn(['country', 'state', 'city']);
        });
    }
};
