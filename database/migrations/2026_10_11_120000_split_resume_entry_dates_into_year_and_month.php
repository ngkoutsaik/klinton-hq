<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('resume_entries', function (Blueprint $table) {
            $table->unsignedSmallInteger('start_year')->nullable()->after('description');
            $table->unsignedTinyInteger('start_month')->nullable()->after('start_year');
            $table->unsignedSmallInteger('end_year')->nullable()->after('start_month');
            $table->unsignedTinyInteger('end_month')->nullable()->after('end_year');
        });

        DB::table('resume_entries')->update([
            'start_year' => DB::raw('YEAR(start_date)'),
            'start_month' => DB::raw('MONTH(start_date)'),
            'end_year' => DB::raw('YEAR(end_date)'),
            'end_month' => DB::raw('MONTH(end_date)'),
        ]);

        Schema::table('resume_entries', function (Blueprint $table) {
            $table->unsignedSmallInteger('start_year')->nullable(false)->change();
            $table->dropColumn(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resume_entries', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('description');
            $table->date('end_date')->nullable()->after('start_date');
        });

        // Year-only entries come back as January, and every date as the 1st of its month
        DB::table('resume_entries')->update([
            'start_date' => DB::raw('MAKEDATE(start_year, 1) + INTERVAL (COALESCE(start_month, 1) - 1) MONTH'),
            'end_date' => DB::raw('IF(end_year IS NULL, NULL, MAKEDATE(end_year, 1) + INTERVAL (COALESCE(end_month, 1) - 1) MONTH)'),
        ]);

        Schema::table('resume_entries', function (Blueprint $table) {
            $table->date('start_date')->nullable(false)->change();
            $table->dropColumn(['start_year', 'start_month', 'end_year', 'end_month']);
        });
    }
};
