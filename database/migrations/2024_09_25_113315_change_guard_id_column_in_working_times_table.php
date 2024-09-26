<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('working_times', function (Blueprint $table) {
            $table->dropForeign(['guard_id']);
            $table->dropColumn('guard_id');
            $table->unsignedBigInteger('guard_number')->nullable()->after('id');
            $table->foreignId('site_id')->after('guard_number')->nullable()->constrained('sites')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('working_times', function (Blueprint $table) {
        });
    }
};
