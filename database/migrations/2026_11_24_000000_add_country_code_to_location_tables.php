<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['divisions', 'districts', 'upazilas', 'unions'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('country_code', 2)->default('BD')->after('id')->index();
            });
        }
    }

    public function down(): void
    {
        foreach (['divisions', 'districts', 'upazilas', 'unions'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropIndex(['country_code']);
                $table->dropColumn('country_code');
            });
        }
    }
};
